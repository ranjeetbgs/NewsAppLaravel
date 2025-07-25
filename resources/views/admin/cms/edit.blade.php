@extends('admin/layout/app')
@section('content')
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/quill/editor.css')}}" />
<script src="{{ asset('admin-assets/js/ckeditor.js')}}"></script>
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/category')}}"> {{__('lang.admin_cms')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_edit_cms')}}</h4>
    <div class="row">
        <div class="col-xl">
            <form method="POST" action="{{url('admin/update-cms')}}" id="edit-record" onsubmit="return validateCms('edit-record');" method="POST" enctype="multipart/form-data">
            @csrf
                <div class="card mb-4">
                    <div class="card-body">
                
                        <input type="hidden" id="id" name="id" value="{{$row->id}}"></input>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3 display-inline-block width-74-percent">
                                    <label class="form-label" for="title">{{__('lang.admin_title')}} <span class="required">*</span></label>
                                    <input type="text" class="form-control" placeholder="{{__('lang.admin_title_placeholder')}}"  name="title" value="{{$row->title}}" onkeypress="setValue('meta_char',this.value);" />
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3 display-inline-block width-74-percent">
                                    <label class="form-label" for="title">{{__('lang.admin_description')}}</label>
                                    <textarea class="form-control" name="description" id="editor" name="{{__('lang.description')}}" placeholder="{{__('lang.admin_description_placeholder')}}">{!! $row->description !!}</textarea>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="mb-1">
                                    <label class="form-label" for="basic-icon-default-uname">{{__('lang.admin_image')}}</label>
                                    <div class="d-flex">
                                    <img src="{{ asset('uploads/cms')}}/{{$row->image}}" id="image-preview_{{$row->id}}" class="rounded me-50" alt="profile image" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                                    <div class="mt-75 ms-1">
                                        <label class="btn btn-primary me-75 mb-0" for="change-picture">
                                        <span class="d-none d-sm-block">{{__('lang.admin_upload')}}</span>
                                        <input class="form-control" type="file" name="image" id="change-picture" hidden accept="image/png, image/jpeg, image/jpg" name="image" onclick="showImagePreview('change-picture','image-preview',1000,1000);"/>
                                        <span class="d-block d-sm-none">
                                            <i class="me-0" data-feather="edit"></i>
                                        </span>
                                        </label>
                                        <p>{{__('lang.admin_cms_image_resolution')}}</p>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex flex-sm-row flex-column mt-2">
                                <button type="submit" class="btn btn-primary mb-1 mb-sm-0 me-0 me-sm-1">{{__('lang.admin_button_save_changes')}}</button>
                                <a href="{!! url('admin/cms') !!}" class="btn btn-outline-secondary">{{__('lang.admin_button_back')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <h5 class="fw-bold"> {{__('lang.admin_seo_details')}}</h5>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3 display-inline-block width-74-percent">
                                    <label class="form-label" for="meta_char">{{__('lang.admin_title')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <input type="text" class="form-control" placeholder="{{__('lang.admin_title_placeholder')}}" id="meta_char" name="meta_char" value="{{$row->meta_char}}"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 display-inline-block width-74-percent">
                                    <label class="form-label" for="meta_keywords">{{__('lang.admin_keyword')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <input type="text" id="meta_keywords" class="form-control" name="meta_keywords" placeholder="{{__('lang.admin_keyword_placeholder')}}" value="{{$row->meta_keywords}}"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 display-inline-block width-74-percent">
                                    <label class="form-label" for="meta_desc">{{__('lang.admin_description')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <textarea class="form-control" name="meta_desc" id="editormeta" placeholder="{{__('lang.admin_description_placeholder')}}" value="{{$row->meta_desc}}">{{$row->meta_desc}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex flex-sm-row flex-column mt-2">
                                <button type="submit" class="btn btn-primary mb-1 mb-sm-0 me-0 me-sm-1">{{__('lang.admin_button_save_changes')}}</button>
                                <a href="{!! url('admin/cms') !!}" class="btn btn-outline-secondary">{{__('lang.admin_button_back')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    const { ClassicEditor, SourceEditing, Markdown } = CKEDITOR;
    ClassicEditor
    .create(document.querySelector('#editor-notusethis'), {
        minHeight: '300px',
        plugins: [ SourceEditing, Markdown, /* ... */ ],
        toolbar: [ 'sourceEditing', /* ... */ ]
    });
</script>
@endsection