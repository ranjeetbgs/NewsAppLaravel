<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Visibility;
use App\Models\Language;
use App\Models\BlogImage;
use App\Models\DeviceToken;
use App\Models\BlogBookmark;
use App\Models\LanguageCode;
use App\Models\BlogAnalytic;

use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Http\Requests\Blog\UpdateBlogTranslationRequest;

use App\Http\Requests\Blog\StoreQuoteRequest;
use App\Http\Requests\Blog\UpdateQuoteRequest;
use App\Http\Requests\Blog\UpdateQuoteTranslationRequest;
use Illuminate\Support\Facades\Session;
use Kyslik\ColumnSortable\Sortable;
use DB;

class BlogController extends Controller
{
    /**
     * Display a listing of the blog.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
       
        Session::regenerate();   
        $data['result'] = Blog::getLists($request->all());
            $data['category'] = Category::where('status',1)->where('parent_id',0)->get();
            $data['visibility'] = Visibility::where('status',1)->get();
            return view('admin.blog.index',$data);
      
    }

    /**
     * Show the form for creating a new blog.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        try {
                     
            $data['images'] = BlogImage::where('session_id',Session::get('session_id'))->orderBy('order','ASC')->get();
            $data['categories'] = Category::where('parent_id',0)->orderBy('name','ASC')->get();
            $data['visibility'] = Visibility::latest('created_at')->get();
            return view('admin.blog.create_'.$type.'',$data);
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBlogRequest $request)
    {       
        
        $validated = $request->validated();

        
            $added = Blog::addUpdate($request->all());

            return $this->sendResponse($added, __('lang.message_data_retrived_successfully'));
            if($added['status']==true){
                return redirect('admin/blog')->with('success', $added['message']); 
            }
            else{
                return redirect()->back()->with('error', $added['message']);
            } 
        // try{
        //     $validated = $request->validated();
        //     $added = Blog::addUpdate($request->all());
        //     if($added['status']==true){
        //         return redirect('admin/blog')->with('success', $added['message']); 
        //     }
        //     else{
        //         return redirect()->back()->with('error', $added['message']);
        //     }
        // }
        // catch(\Exception $ex){
        //     return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        // }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeQuote(StoreQuoteRequest $request)
    {        
        try{
            $validated = $request->validated();
            
            $added = Blog::addUpdateQuote($request->all());
            if($added['status']==true){
                return redirect('admin/blog')->with('success', $added['message']); 
            }
            else{
                return redirect()->back()->with('error', $added['message']);
            }
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  type $type, id  $id 
     * @return \Illuminate\Http\Response
     */
    public function edit($type,$id)
    {        
        try {
            $data['images'] = BlogImage::where('blog_id',$id)->orderBy('order','ASC')->get();
            
            $data['categories'] = Category::where('parent_id',0)->orderBy('name','ASC')->get();
            $data['subcategory'] = array();
            $data['visibility'] = Visibility::latest('created_at')->get();
            $data['voice_accent'] = config('constant.voice_accent');
            $data['speech_voice'] = config('constant.speech_voice');
            $data['row'] = Blog::getDetail($id);
            if($data['row']!=''){
                if(isset($data['row']->categoryArr) && count($data['row']->categoryArr)){
                    $data['subcategory'] = Category::whereIn('parent_id',$data['row']->categoryArr)->get();
                }
            }
            // echo json_encode($data);exit;
            return view('admin.blog.edit_'.$type.'',$data);
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogRequest $request)
    {
        try{
            $validated = $request->validated();

            $updated = Blog::addUpdate($request->all(),$request->input('id'));
            if($updated['status']==true){
                return redirect('admin/blog')->with('success', $updated['message']); 
            }
            else{
                return redirect()->back()->with('error', $updated['message']);
            } 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQuoteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateQuote(UpdateQuoteRequest $request)
    {        
        try{
            $validated = $request->validated();
            $updated = Blog::addUpdateQuote($request->all(),$request->input('id'));
            if($updated['status']==true){
                return redirect('admin/blog')->with('success', $updated['message']); 
            }
            else{
                return redirect()->back()->with('error', $updated['message']);
            } 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Remove the specified resource from Blog.
     * @param  Request $request
     * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        try{
            $deleted = Blog::deleteRecord($id);
            if($deleted['status']==true){
                return redirect()->back()->with('success', $deleted['message']); 
            }
            else{
                return redirect()->back()->with('error', $deleted['message']);
            } 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Remove the specified category from storage.
     *
     * @param  id  $id
     * @return \Illuminate\Http\Response
    **/
    public function changeStatus($id,$status)
    {
        try{
            $updated = Blog::changeStatus($status,$id);
            if($updated['status']){
                return redirect()->back()->with('success', $updated['message']); 
            }
            else{
                return redirect()->back()->with('error', $updated['message']);
            } 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Get translations of specified category from storage.
     *
     * @param  id $id
     * @return \Illuminate\Http\Response
    **/
    public function translation($id)
    {
        try{
            $data['detail'] = Blog::getDetail($id);
            $data['languages'] = Blog::getTranslation($id);
            return view('admin/blog.translation',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Update the translation of specified category in storage.
     *
     * @param  \App\Http\Requests\UpdateBlogTranslationRequest  $request
     * @param  id  $id
     * @return \Illuminate\Http\Response
    **/
    public function updateTranslation(UpdateBlogTranslationRequest $request,$id)
    {
        $validated = $request->validated();
        $translationUpdated = Blog::updateTranslation($request->all(),$id);
        if($translationUpdated['status']==true){
            return redirect('admin/blog')->with('success', $translationUpdated['message']); 
        }
        else{
            return redirect()->back()->with('error', $translationUpdated['message']);
        } 
    }
    /**
     * Update the translation of specified category in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
    **/
    public function getSubcategories(Request $request)
    {
        $post = $request->all();
        $subcategory = Category::whereIn('parent_id',$post['category_id'])->get();
        $sendArr = array(
            'subcategory'=>$subcategory,
        );
        if(isset($post['sub_category_id']) && count($post['sub_category_id'])){
            $sendArr['sub_cat_id'] = $post['sub_category_id']; 
        }

        $data['html'] = view('admin.blog.partials.subcategory')->with($sendArr)->render();
        $response = [
            'status' => true,
            'message' => "Data fetched successfully.",
            'data' => $data
        ];
        return response($response);
    }

    public function storeImage(Request $request)
    {    
        $files = $request->file('file'); 
            $uploadImage = \Helpers::uploadFilesAfterResizeCompressOriginalName($files,'blog');
            
            if($uploadImage['status']==true){
                $postArr = array(
                    'session_id'=>Session::get('session_id'),
                    'image'=>$uploadImage['file_name'],
                    'created_at'=>date('Y-m-d H:i:s')
                );
                if(isset($request->blog_id) && $request->blog_id!=0){
                    $postArr['blog_id'] = $request->blog_id;
                }else{
                    $postArr['session_id'] = Session::get('session_id');
                }
                $image = BlogImage::insertGetId($postArr);
            } 
            if(isset($request->blog_id) && $request->blog_id!=0){ 
                $blog_images = BlogImage::where('blog_id',$request->blog_id)->orderBy('order','ASC')->get();
            }else{
                $blog_images = BlogImage::where('session_id',Session::get('session_id'))->orderBy('order','ASC')->get();
            }
            $data['uploadImage'] = $uploadImage;
            $data['session_id'] = Session::get('session_id');
            $data['blog_images'] = $blog_images;
            $data['html'] = view('admin.blog.partials.image_preview')->with(array('images'=>$blog_images))->render();
            $response = $this->sendResponse($data,__('lang.message_data_retrived_successfully'));
            return $response;
        // try{
            
        //     // $blog_images = BlogImage::where('session_id',Session::get('session_id'))->get();
        //     // $data['html'] = view('admin.blog.partials.image_preview')->with(array('images'=>$blog_images))->render();
        //     // // echo json_encode($data);exit;
        //     // $response = $this->sendResponse($blog_images,__('lang.message_data_retrived_successfully'));
            
        //     // return $response;
        //     // $validated = $request->validated();
            
        //     // $added = Blog::addUpdateQuote($request->all());
        //     // if($added['status']==true){
        //     //     return redirect('admin/blog')->with('success', $added['message']); 
        //     // }
        //     // else{
        //     //     return redirect()->back()->with('error', $added['message']);
        //     // }
        // }
        // catch(\Exception $ex){
        //     return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        // }
    }

    public function removeImage(Request $request)
    {     
        $files = $request->all();
            // print_r($files);
            $item_id = 0;
            $image = BlogImage::where('id',$request->image_id)->first();
            if($image!=''){
                BlogImage::where('id',$request->image_id)->delete();
                $blog_images = BlogImage::where('session_id',Session::get('session_id'))->orderBy('order','ASC')->get();
                if($image->blog_id!=0){
                    $blog_images = BlogImage::where('blog_id',$image->blog_id)->orderBy('order','ASC')->get();
                }   
                // echo json_encode($blog_images);exit;  
                $data['blog_images'] = $blog_images;
                $data['html'] = view('admin.blog.partials.image_preview')->with(array('images'=>$blog_images))->render();
                $response = $this->sendResponse($data,__('lang.message_data_retrived_successfully'));
                return $response;            
            }
            return $this->sendError(__('lang.message_something_went_wrong'));  
        // try{
            
        //     // $validated = $request->validated();
            
        //     // $added = Blog::addUpdateQuote($request->all());
        //     // if($added['status']==true){
        //     //     return redirect('admin/blog')->with('success', $added['message']); 
        //     // }
        //     // else{
        //     //     return redirect()->back()->with('error', $added['message']);
        //     // }
        // }
        // catch(\Exception $ex){
        //     return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        // }
    }

    public function removeImageByName(Request $request)
    {     
        $files = $request->all();
            // print_r($files);
            $item_id = 0;
            if(isset($request->blog_id) && $request->blog_id!=0){
                $image = BlogImage::where('image',$request->filename)->where('blog_id',$request->blog_id)->orderBy('id','DESC')->first();
            }else{
                $image = BlogImage::where('image',$request->filename)->where('session_id',Session::get('session_id'))->orderBy('id','DESC')->first();
            }     
            // echo json_encode($image);exit;          
            if($image!=''){
                BlogImage::where('id',$image->id)->delete();
                $blog_images = BlogImage::where('session_id',Session::get('session_id'))->orderBy('order','ASC')->get();
                if($image->blog_id!=0){
                    $blog_images = BlogImage::where('blog_id',$image->blog_id)->orderBy('order','ASC')->get();
                }   
                // echo json_encode($blog_images);exit;  
                $data['blog_images'] = $blog_images;
                $data['html'] = view('admin.blog.partials.image_preview')->with(array('images'=>$blog_images))->render();
                $response = $this->sendResponse($data,__('lang.message_data_retrived_successfully'));
                return $response;            
            }
            return $this->sendError(__('lang.message_something_went_wrong'));  
        // try{
            
        //     // $validated = $request->validated();
            
        //     // $added = Blog::addUpdateQuote($request->all());
        //     // if($added['status']==true){
        //     //     return redirect('admin/blog')->with('success', $added['message']); 
        //     // }
        //     // else{
        //     //     return redirect()->back()->with('error', $added['message']);
        //     // }
        // }
        // catch(\Exception $ex){
        //     return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        // }
    }

    /**
     * Update order storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sorting(Request $request)
    {
        try{
            $input = $request->all();
            if(isset($input['blog_id']) && $input['blog_id']!=0){
                $posts = BlogImage::where('blog_id',$input['blog_id'])->get();
            }else{
                $posts = BlogImage::where('session_id',Session::get('session_id'))->get();
            }          
            foreach ($posts as $post) {
                foreach ($request->order as $order) {
                    if ($order['id'] == $post->id) {
                        $c = BlogImage::where('id',$post->id)->update(['order' => $order['position']]);                        
                    }
                }
            }
            $response = [
                'status' => true,
                'message' => __('lang.message_data_retrived_successfully'),
                'data' => []
            ];
            return response($response);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }


    /**
     * Send Notification from storage.
     *
     * @param  id $id
     * @return \Illuminate\Http\Response
    **/
    public function sendNotification(Request $request, $id)
    { 
        \Log::debug($id);
        if(!is_numeric($id))
        {
            $blog = Blog::where('title',$id)->first();
            if($blog)
            {
                $id = $blog->id;
                return $this->sendResponse([$blog],'lang.message_notification_sent_successfully');
            }
            return $this->sendError('blog not found');

        }

        $isApiRequest = $request->wantsJson();
        if(setting('one_signal_key')==''){
            if($isApiRequest) return $this->sendError(__('lang.message_one_signal_key_not_found'));
            return redirect()->back()->with('error',__('lang.message_one_signal_key_not_found'));
        }else{
            $blog = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','voice', 'accent_code','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image')->where('id',$id)->where('status',1)->with('blog_category')->with('blog_sub_category')->first();
            $image = url('uploads/setting/'.setting('app_logo'));
            if($blog){
                $blog->images = \Helpers::getBlogImages($blog->id,'327x250');
                if($blog->type=='post'){
                    if(count($blog->images)){
                        $image = $blog->images[0];
                    }
                }else{
                    $image = url('uploads/blog/'.$blog->background_image);
                }                
                if($blog->background_image!=''){
                    $blog->background_image = url('uploads/blog/'.$blog->background_image);
                }
            }
            $player_id = array();
            $token = DeviceToken::select('player_id', DB::raw('MAX(id) as max_id,is_notification_enabled'))->groupBy('player_id')->orderBy('max_id', 'DESC')->get();
            if(count($token)){
                foreach($token as $detail){
                    if($detail->player_id!='' || $detail->player_id!=null || $detail->player_id!="null"){
                        if($detail->is_notification_enabled==1){
                            if(!in_array($detail->player_id,$player_id)){
                                array_push($player_id,$detail->player_id);
                            }
                        }
                    }                   
                }
            }
            $status = \Helpers::sendNotification($blog->title,$blog->description,$image,$blog,$player_id);
            if ($status === 200) {
                if($isApiRequest) return $this->sendResponse([],'lang.message_notification_sent_successfully');
                return redirect()->back()->with('success', __('lang.message_notification_sent_successfully')); 
            } else {
                if($isApiRequest) return $this->sendError(__('lang.message_error_while_sending'));
                return redirect()->back()->with('error', __('lang.message_error_while_sending'));
                
            }   
        }    
            
        // try{
        //     $blog = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','voice', 'accent_code','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image')->where('id',$id)->where('status',1)->with('blog_category')->with('blog_sub_category')->first();
        //     $image = url('uploads/setting/'.setting('app_logo'));
        //     if($blog){
        //         $blog->images = \Helpers::getBlogImages($blog->id,'327x250');
        //         if($blog->type=='post'){
        //             if(count($blog->images)){
        //                 $image = $blog->images[0];
        //             }
        //         }else{
        //             $image = url('uploads/blog/'.$blog->background_image);
        //         }                
        //         if($blog->background_image!=''){
        //             $blog->background_image = url('uploads/blog/'.$blog->background_image);
        //         }
        //     }
        //     $player_id = array();
        //     $token = DeviceToken::select('player_id', DB::raw('MAX(id) as max_id,is_notification_enabled'))->groupBy('player_id')->orderBy('max_id', 'DESC')->get();
        //     if(count($token)){
        //         foreach($token as $detail){
        //             if($detail->player_id!='' || $detail->player_id!=null || $detail->player_id!="null"){
        //                 if($detail->is_notification_enabled==1){
        //                     if(!in_array($detail->player_id,$player_id)){
        //                         array_push($player_id,$detail->player_id);
        //                     }
        //                 }
        //             }                   
        //         }
        //     }
        //     $status = \Helpers::sendNotification($blog->title,$blog->description,$image,$blog,$player_id);
        //     if ($status === 200) {
        //         return redirect()->back()->with('success', __('lang.message_notification_sent_successfully')); 
        //     } else {
        //         return redirect()->back()->with('error', __('lang.message_error_while_sending'));
        //     }
        // }
        // catch(\Exception $ex){
        //     return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        // }
    }

    /**
     * Display a listing of the blog.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function analytics(Request $request,$id)
    {
        try{
            $pagination = (isset($search['perpage']))?$search['perpage']:config('constant.pagination');
            $data['views'] = BlogAnalytic::where('type','view')->where('blog_id',$id)->with('user')->paginate($pagination)->appends('perpage', $pagination);
            $data['bookmarks'] = BlogBookmark::where('blog_id',$id)->with('user')->paginate($pagination)->appends('perpage', $pagination);
            $data['shares'] = BlogAnalytic::where('type','share')->where('blog_id',$id)->with('user')->paginate($pagination)->appends('perpage', $pagination);
            return view('admin.blog.analytics',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
}
