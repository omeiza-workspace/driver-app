<?php

namespace App\Service;

use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class BaseService implements BaseServiceInterface
{

    protected EloquentRepositoryInterface $baseRepository;

    public function __construct(EloquentRepositoryInterface $baseRepository)
    {
        $this->baseRepository = $baseRepository;
    }

    public function getAll(array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $groupBy = []): Collection|LengthAwarePaginator
    {
        return $this->baseRepository->getAll(relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, onlyTrashed: $onlyTrashed, withTrashed: $withTrashed, withCountQuery: $withCountQuery, groupBy: $groupBy);
    }

    public function getBy(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $whereHasRelations = [], array $withAvgRelations = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $appends = [], array $groupBy = []): Collection|LengthAwarePaginator
    {
        return $this->baseRepository->getBy(criteria: $criteria, searchCriteria: $searchCriteria, whereInCriteria: $whereInCriteria, whereBetweenCriteria: $whereBetweenCriteria, whereHasRelations: $whereHasRelations, withAvgRelations: $withAvgRelations, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, onlyTrashed: $onlyTrashed, withTrashed: $withTrashed, withCountQuery: $withCountQuery, appends: $appends, groupBy: $groupBy);
    }

    public function create(array $data): ?Model
    {
        return $this->baseRepository->create(data: $data);
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        return $this->baseRepository->update(id: $id, data: $data);
    }

    public function updatedBy(array $criteria = [], array $whereInCriteria = [], array $data = [], bool $withTrashed = false)
    {
        return $this->baseRepository->updatedBy(criteria: $criteria, whereInCriteria: $whereInCriteria, data: $data, withTrashed: $withTrashed);
    }

    public function findOne(int|string $id, array $withAvgRelations = [], array $relations = [], array $whereHasRelations = [], array $withCountQuery = [], bool $withTrashed = false, bool $onlyTrashed = false): ?Model
    {
        return $this->baseRepository->findOne(id: $id, relations: $relations, withAvgRelations: $withAvgRelations, whereHasRelations:$whereHasRelations, withCountQuery: $withCountQuery, withTrashed: $withTrashed, onlyTrashed: $onlyTrashed);
    }

    public function findOneBy(array $criteria = [], array $whereInCriteria = [], array $withAvgRelations = [], array $whereHasRelations = [], array $relations = [], array $orderBy = [], array $withCountQuery = [], bool $withTrashed = false, bool $onlyTrashed = false): ?Model
    {
        return $this->baseRepository->findOneBy(criteria: $criteria, whereInCriteria: $whereInCriteria, withAvgRelations: $withAvgRelations, relations: $relations, whereHasRelations:$whereHasRelations,withCountQuery: $withCountQuery, orderBy: $orderBy, withTrashed: $withTrashed, onlyTrashed: $onlyTrashed);
    }

    public function delete(int|string $id): bool
    {
        return $this->baseRepository->delete(id: $id);
    }

    public function deleteBy(array $criteria): bool
    {
        return $this->baseRepository->deleteBy(criteria: $criteria);
    }

    public function permanentDelete(int|string $id): bool
    {
        return $this->baseRepository->permanentDelete(id: $id);
    }

    public function permanentDeleteBy(array $criteria): bool
    {
        return $this->baseRepository->permanentDeleteBy(criteria: $criteria);
    }

    public function restoreData(int|string $id): Mixed
    {
        return $this->baseRepository->restoreData(id: $id);
    }

    //custom
    public function index(array $criteria = [], array $relations = [], array $whereHasRelations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = [], array $appends = [], array $groupBy = []): Collection|LengthAwarePaginator
    {
        $data = [];
        if (array_key_exists('status', $criteria) && $criteria['status'] !== 'all') {
            $data['is_active'] = $criteria['status'] == 'active' ? 1 : 0;
        }
        $searchData = [];
        if (array_key_exists('search', $criteria) && $criteria['search'] != '') {
            $searchData['fields'] = ['name'];
            $searchData['value'] = $criteria['search'];
        }
        $whereInCriteria = [];
        $whereBetweenCriteria = [];
        return $this->baseRepository->getBy(criteria: $data, searchCriteria: $searchData, whereInCriteria: $whereInCriteria, whereBetweenCriteria: $whereBetweenCriteria, whereHasRelations: $whereHasRelations, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, withCountQuery: $withCountQuery, appends: $appends, groupBy: $groupBy); // TODO: Change the autogenerated stub
    }

    public function statusChange(string|int $id, array $data): ?Model
    {
        $data = [
            'is_active' => $data['status'] == 0 ? $data['status'] : 1
        ];
        return $this->baseRepository->update(id: $id, data: $data);
    }

    public function defaultStatusChange(string|int $id, array $data): ?Model
    {
        $data = [
            'is_default' => $data['status'] == 0 ? $data['status'] : 1
        ];
        $baseModel = $this->baseRepository->update(id: $id, data: $data);
        if ($baseModel?->is_default == true) {
            $this->baseRepository->updatedBy(criteria: [['id', '!=', $baseModel?->id]], data: ['is_default' => 0]);
        }
        return $baseModel;
    }

    public function trashedData(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator
    {
        $searchData = [];
        if (array_key_exists('search', $criteria) && $criteria['search'] != '') {
            $searchData['fields'] = ['name'];
            $searchData['value'] = $criteria['search'];
        }
        return $this->baseRepository->getBy(searchCriteria: $searchData, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, onlyTrashed: true, withCountQuery: $withCountQuery);
    }
}
