@section('title', translate('parcel_Weights'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">

                <div class="col-12">

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <h2 class="fs-22 text-capitalize">{{ translate('deleted_weight_range_list') }}</h2>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_parcel_weight_ranges') }} : </span>
                            <span class="text-primary fs-16 fw-bold">{{ $weights->total() }}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="{{url()->full()}}"
                                              class="search-form search-form_style-two" method="GET">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" class="theme-input-style search-form__input"
                                                       value="{{$search}}" name="search" id="search"
                                                       placeholder="{{translate('search_here_by_Parcel_Weight_Name')}}">
                                            </div>
                                            <button type="submit"
                                                    class="btn btn-primary">{{ translate('search') }}</button>
                                        </form>

                                    </div>

                                    <div class="tmodel/inable-responsive mt-3 text-center">
                                        <table class="table table-borderless align-middle">
                                            <thead class="table-light align-middle">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th class="text-capitalize name">{{ translate('parcel_weight_range') }}</th>
                                                <th class="text-center action">{{ translate('action') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse ($weights as $weight)
                                                <tr id="hide-row-{{$weight->id}}">
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td class="name">{{ ($weight->min_weight+0).'-'.($weight->max_weight+0).' '.$weightUnit}}</td>
                                                    <td class="action">
                                                        <div
                                                            class="d-flex justify-content-center gap-2 align-items-center">
                                                            <button
                                                            data-route="{{ route('admin.parcel.attribute.weight.restore', ['id' => $weight->id]) }}"
                                                            data-message="{{ translate('Want_to_recover_this_weight?_') . translate('if_yes,_this_weight_will_be_available_again_in_the_Weight_List')}}"
                                                            class="btn btn-outline-primary btn-action restore-data">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                            <button
                                                                data-id="delete-{{ $weight->id }}"
                                                                data-message="{{ translate('want_to_permanent_delete_this_weight?') }} {{translate('you_cannot_revert_this_action')}}"
                                                                type="button"
                                                                class="btn btn-outline-danger btn-action form-alert">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>

                                                            <form
                                                                action="{{ route('admin.parcel.attribute.weight.permanent-delete', ['id'=>$weight->id]) }}"
                                                                id="delete-{{ $weight->id }}" method="post">
                                                                @csrf
                                                                @method('delete')
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                                            <img src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}" alt="" width="100">
                                                            <p class="text-center">{{translate('no_data_available')}}</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {!! $weights->links() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')

@endpush
