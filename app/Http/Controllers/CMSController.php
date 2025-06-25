<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsContent;

class CMSController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request,$slug)
    {
    	$content = CmsContent::where('page_title',$slug)->get()->first();
    	
        return view('cms',compact('content'));
    }
}
