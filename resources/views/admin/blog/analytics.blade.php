@extends('admin/layout/app')
@section('content')
<script src="{{ asset('admin-assets/js/ckeditor.js')}}"></script>
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/tagify/tagify.css')}}" />

<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/select2/select2.css')}}" />


<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 display-inline-block"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/blog')}}"> {{__('lang.admin_blog')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_analytics')}}</h4>
    <div class="row">
        <div class="col">
            <div class="card mb-3">
                
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link active"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-personal"
                            role="tab"
                            aria-selected="true"
                            >
                            {{__('lang.admin_blog_views')}}({{count($views)}})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-account"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_blog_bookmark')}}({{count($bookmarks)}})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-source"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_blog_share')}}({{count($shares)}})
                            </button>
                        </li>
                        <!-- <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-social"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_visibility')}}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-voting"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_voting_pool_question')}}
                            </button>
                        </li> -->
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{__('lang.admin_name')}}</th>
                                    <th>{{__('lang.admin_viewed_at')}}</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @if(count($views) > 0) 
                                    @foreach($views as $view) 
                                        <tr>
                                            <td>
                                            @if(isset($view->user) && $view->user!=''){{$view->user->name}}@else Guest @endif
                                            </td>
                                            <td>
                                                {{date("d-m-Y",strtotime($view->created_at))}}</br>
                                                <span>{{date("h:i A",strtotime($view->created_at))}}</span>
                                            </td>
                                        </tr> 
                                    @endforeach 
                                @else 
                                    <tr>
                                        <td colspan="2" class="record-not-found">
                                            <span>{{__('lang.admin_record_not_found')}}</span>
                                        </td>
                                    </tr> 
                                @endif 
                            </tbody>
                        </table>   
                        <div class="card-footer">
                            <div class="pagination" style="float: right;">
                                {{$views->withQueryString()->links('pagination::bootstrap-4')}}
                            </div>
                        </div>                    
                    </div>
                    <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">                    
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{__('lang.admin_name')}}</th>
                                    <th>{{__('lang.admin_viewed_at')}}</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @if(count($bookmarks) > 0) 
                                    @foreach($bookmarks as $bookmark) 
                                        <tr>
                                            <td>
                                                @if(isset($bookmark->user) && $bookmark->user!=''){{$bookmark->user->name}}@else Guest @endif
                                            </td>
                                            <td>
                                                {{date("d-m-Y",strtotime($bookmark->created_at))}}</br>
                                                <span>{{date("h:i A",strtotime($bookmark->created_at))}}</span>
                                            </td>
                                        </tr> 
                                    @endforeach 
                                @else 
                                    <tr>
                                        <td colspan="2" class="record-not-found">
                                            <span>{{__('lang.admin_record_not_found')}}</span>
                                        </td>
                                    </tr> 
                                @endif 
                            </tbody>
                        </table>   
                        <div class="card-footer">
                            <div class="pagination" style="float: right;">
                                {{$bookmarks->withQueryString()->links('pagination::bootstrap-4')}}
                            </div>
                        </div>                
                    </div>
                    <div class="tab-pane fade" id="form-tabs-source" role="tabpanel">                    
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{__('lang.admin_name')}}</th>
                                    <th>{{__('lang.admin_viewed_at')}}</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @if(count($shares) > 0) 
                                    @foreach($shares as $share) 
                                        <tr>
                                            <td>
                                            @if(isset($share->user) && $share->user!=''){{$share->user->name}}@else Guest @endif
                                            </td>
                                            <td>
                                                {{date("d-m-Y",strtotime($share->created_at))}}</br>
                                                <span>{{date("h:i A",strtotime($share->created_at))}}</span>
                                            </td>
                                        </tr> 
                                    @endforeach 
                                @else 
                                    <tr>
                                        <td colspan="2" class="record-not-found">
                                            <span>{{__('lang.admin_record_not_found')}}</span>
                                        </td>
                                    </tr> 
                                @endif 
                            </tbody>
                        </table>      
                        <div class="card-footer">
                            <div class="pagination" style="float: right;">
                                {{$shares->withQueryString()->links('pagination::bootstrap-4')}}
                            </div>
                        </div>             
                    </div>
                    <!-- <div class="tab-pane fade" id="form-tabs-social" role="tabpanel">
                        <div class="row g-3">
                            
                        </div>
                    </div>
                    <div class="tab-pane fade" id="form-tabs-voting" role="tabpanel">
                        
                    </div> -->
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection