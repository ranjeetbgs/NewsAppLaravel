<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Blog;
use App\Models\BlogImage;

use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Services\FirebaseService;

class BlogController extends Controller
{

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        
       $query = Blog::with(['image','blog_category']);

       if($request->s)
       {
        // $query = $query->where('title', 'like', '%' . $request->s . '%')->orWhere('description', 'like', '%' . $request->s . '%');

        $query = $query->whereRaw("MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$request->s]);
       }

       if($request->category_id)
       {

            $category_id = $request->category_id;

           $query = $query->whereHas("blog_category", function($q) use ($category_id)
            {
                return $q->where('category_id', '=', $category_id);
            });
       }
    
       $limit = $request->has('limit') ? $request->limit : 10;

       if($request->is_featured) $query = $query->where('is_featured',1);

       $blogs = $query->where('status',1)->orderBy('id', 'desc')->paginate($limit);
       $blogs->setPath('https://www.newspepperapp.in/api/blogs');
       $blogs->appends($request->input());
       return $this->sendResponse($blogs, '');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {   
        
        $validated = $request->validated();

        $is_post_exist = Blog::where('post_id', $request->post_id)->first();

        if($is_post_exist) return $this->update($request, $is_post_exist->id);
        
        $should_send_notification = $request['should_send_notification'];
        unset($request['should_send_notification']);

        $added = Blog::addUpdate($request->all());
        $blog = Blog::find($added['blog_id']);
        
        if($request->image)
        {
            $blogImage = new BlogImage;
                $blogImage->image = $request->image;
                $blogImage->blog_id = $blog->id;
                $blogImage->save();
        }
        

        if($added['status']) 
        {

            if($should_send_notification=="1")
            {

             $this->firebase->sendNotificationAllByBlogId($blog->id);

            }
            


     return $this->sendResponse($added, '');
        }


           
        else return $this->sendError($added['message']);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBlogRequest $request, $id)
    { 
        $should_send_notification = $request->should_send_notification;
        unset($request['should_send_notification']);
        try{
            $validated = $request->validated();
            $updated = Blog::addUpdate($request->all(),$id);
            $blog = Blog::find($updated['blog_id']);
        //return $this->sendResponse($blog, '');

        if($request->image)
        {
            
            $blogImage = BlogImage::where('blog_id',$blog->id)->first();

            if($blogImage) 
            {
                $blogImage->image = $request->image;
                $blogImage->save();

            }
            else
            {
                $blogImage = new BlogImage;
                $blogImage->image = $request->image;
                $blogImage->blog_id = $blog->id;
                $blogImage->save();
            }

           
                    
            
        }
       
         
        
            if($updated['status']){
                if($should_send_notification=="1")
            {

             $this->firebase->sendNotificationAllByBlogId($blog->id);

            }
                 return $this->sendResponse($updated, '');


            }
            else{
                return $this->sendError($added['message']);
                
            } 
        }
        catch(\Exception $ex){
            return $this->sendError($ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile());
                  }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
