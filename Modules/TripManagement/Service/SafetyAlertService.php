<?php

namespace Modules\TripManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Modules\TripManagement\Repository\SafetyAlertRepositoryInterface;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;

class SafetyAlertService extends BaseService implements Interface\SafetyAlertServiceInterface
{
    protected $tripRequestService;

    public function __construct(SafetyAlertRepositoryInterface $safetyAlertRepository, TripRequestServiceInterface $tripRequestService)
    {
        parent::__construct($safetyAlertRepository);
        $this->tripRequestService = $tripRequestService;
    }

    public function index(array $criteria = [], array $relations = [], array $whereHasRelations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = [], array $appends = [], array $groupBy = []): Collection|LengthAwarePaginator
    {
        $searchData = [];
        if (array_key_exists('search', $criteria) && $criteria['search'] != '') {
            $searchData['fields'] = ['alert_location', 'resolved_location'];
            $searchData['relations'] = [
                'solvedBy' => ['first_name', 'last_name', 'full_name', 'email', 'user_type'],
                'trip.customer' => ['first_name', 'last_name', 'full_name', 'email',],
                'trip.driver' => ['first_name', 'last_name', 'full_name', 'email',],
                'trip' => ['ref_id']
            ];
            $searchData['value'] = $criteria['search'];
        }
        $criteria = [
            'status' => 'solved'
        ];
        $whereInCriteria = [];
        $whereBetweenCriteria = [];
        return $this->baseRepository->getBy(criteria: $criteria, searchCriteria: $searchData, whereInCriteria: $whereInCriteria, whereBetweenCriteria: $whereBetweenCriteria, whereHasRelations: $whereHasRelations, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, withCountQuery: $withCountQuery, appends: $appends, groupBy: $groupBy); // TODO: Change the autogenerated stub
    }

    public function create(array $data): ?Model
    {
        $tripRequestCurrentStatus = $this->tripRequestService->findOneBy(criteria: ['id' => $data['trip_request_id']])->current_status;
        $mapKey = businessConfig(GOOGLE_MAP_API)?->value['map_api_key_server'] ?? null;
        $response = Http::get(MAP_API_BASE_URI . '/geocode/json?latlng=' . $data['lat'] . ',' . $data['lng'] . '&key=' . $mapKey);
        $attributes = [];
        $attributes['trip_request_id'] = $data['trip_request_id'];
        $attributes['sent_by'] = auth('api')->user()?->id;
        $attributes['alert_location'] = json_decode($response->body())->results[0]->formatted_address ?? 'N/A';
        $attributes['trip_status_when_make_alert'] = $tripRequestCurrentStatus;
        if (array_key_exists('reason', $data)) {
            $attributes['reason'] = json_decode($data['reason']);
        }
        if (array_key_exists('comment', $data)) {
            $attributes['comment'] = $data['comment'];
        }

        return $this->baseRepository->create(data: $attributes);
    }

    public function updatedBy(array $criteria = [], array $whereInCriteria = [], array $data = [], bool $withTrashed = false): ?Model
    {
        $trip = $this->tripRequestService->findOneBy(criteria: ['id' => $criteria['trip_request_id']], relations: ['driver.lastLocations']);
        $mapKey = businessConfig(GOOGLE_MAP_API)?->value['map_api_key_server'] ?? null;
        $response = Http::get(MAP_API_BASE_URI . '/geocode/json?latlng=' . $trip?->driver?->lastLocations?->latitude . ',' . $trip?->driver?->lastLocations?->longitude . '&key=' . $mapKey);
        $attributes = [];
        $attributes['resolved_location'] = json_decode($response->body())->results[0]->formatted_address ?? 'N/A';
        $attributes['status'] = 'solved';
        $attributes['resolved_by'] = $data['resolved_by'];

        return $this->baseRepository->updatedBy(criteria: $criteria, whereInCriteria: $whereInCriteria, data: $attributes, withTrashed: $withTrashed);
    }


    public function export(array $criteria = [], array $relations = [], array $whereHasRelations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): \Illuminate\Support\Collection
    {

        $exportData = $this->index(criteria: $criteria, relations: $relations, whereHasRelations: $whereHasRelations, orderBy: ['created_at' => 'desc'], limit: $limit, offset: $offset);
        return $exportData->map(function ($item) {
            return [
                'Trip Reference Id' => $item->trip->ref_id,
                'Date' => date('d F Y', strtotime($item->created_at)) . ', ' . date('h:i A', strtotime($item->created_at)),
                'Sent By' => $item->sentBy->full_name ? $item->sentBy->first_name . ' ' . $item->sentBy->last_name : 'N/A',
                'Customer' => $item->trip->customer->full_name ? $item->trip->customer->first_name . ' ' . $item->trip->customer->last_name : 'N/A',
                'Driver' => $item->trip->driver->full_name ? $item->trip->driver->first_name . ' ' . $item->trip->driver->last_name : 'N/A',
                'Alert Location' => $item->alert_location,
                'Resolved Location' => $item->resolved_location,
                'Number of Alert' => $item->number_of_alert,
                'Resolved By' => $item?->solvedBy?->user_type == 'admin-employee'
                    ? 'Employee - ' . ($item?->solvedBy?->id
                        ? $item?->solvedBy?->first_name . ' ' . $item?->solvedBy?->last_name
                        : '')
                    : $item?->solvedBy?->user_type,
                'Trip Status When Alert Sent' => $item->trip_status_when_make_alert,

            ];
        });
    }

    public function safetyAlertLatestUserRoute(): string
    {
        $firstSafetyAlertRelation = [
            'sentBy', 'trip'
        ];

        $safetyAlert = $this->baseRepository->findOneBy(criteria: ['status' => PENDING], relations: $firstSafetyAlertRelation, orderBy: ['created_at' => 'desc']);

        $userType = match (true) {
            $safetyAlert?->sentBy?->user_type == 'driver' && ($safetyAlert?->trip?->current_status == 'ongoing' || $safetyAlert?->trip?->current_status == 'accepted') => 'driver-on-trip',
            $safetyAlert?->sentBy?->user_type == 'driver' => 'driver-idle',
            default => 'all-customer',
        };

        return route('admin.fleet-map', ['type' => $userType]) . '?zone_id=' . $safetyAlert?->trip?->zone_id;
    }

}
