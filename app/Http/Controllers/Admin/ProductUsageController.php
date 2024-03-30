<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\ProductUsage;


class ProductUsageController extends Controller
{
    public function __construct() {
        //parent::__construct();
        $this->middleware('permission:product_usage');
    }

    public function index(){
        return View('Admin.ProductUsage.index');
    }

    public function ajaxList(Request $request, ProductUsage $productObj){

        $limit = (int)$request->iDisplayLength;
        $offset = (int)$request->iDisplayStart;

        $searchContents['freetext'] = (string)$request->sSearch;
        if ($request->iSortCol_0 != '') {
            for ($i = 0; $i < $request->iSortingCols; $i++) {
                $iSortCol_ = 'iSortCol_' . $i;
                $sSortDir_ = 'sSortDir_' . $i;
                $sortcol = $request->$iSortCol_;
                $sort[$sortcol] = $request->$sSortDir_;
            }
        } else {
            $sort = null;
        }

        $productsCount = $productObj->getAllProductUsage($searchContents);
        $productTypeData = $productObj->getAllProductUsage($searchContents, $sort, $limit, $offset);
    
        $rows = array();
        $iDisplayStart = $request->iDisplayStart;

        $i=1;
        foreach ($productTypeData as $key => $content) {

            $action = '<div class="dropdown">
            <a href="javascript:void(0)" role="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ri-more-2-fill"></i>
            </a>

            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                <li><a class="dropdown-item" href="'.route('admin.product.usage.edit', ['id' => $content->id]).'">Edit</a></li>
                <li><a class="dropdown-item product_remove" href="javascript:void(0)" data-id="' . $content->id . '">Delete</a></li>
            </ul>
        </div>';

            $isActive = '';
            if ($content->status == 'Active') {
                $isActive = '<a href="javascript:void(0)" data-id="' . $content->id . '" class="product_active tooltipped" data-position="top" title="Active"><i class="ri-checkbox-circle-line align-middle text-success fs-16"></i></a>';
            } else {
                $isActive = '<a href="javascript:void(0)" data-id="' . $content->id . '" class="product_inactive tooltipped" data-position="top" title="Inactive"><i class="ri-checkbox-circle-line align-middle text-danger fs-16"></i></a>';
            }

            $rowData = array(
                $i,
                '<a href="'.route('admin.product.usage.edit', ['id' => $content->id]).'">'.$content->name.'</a>',
                $isActive,
                date("d-m-Y H:i:s",strtotime($content->created_at)),
                $action
            );
            $rows[] = $rowData;
            $i++;
        }

        $json = array(
            'sEcho' => intval($request->sEcho),
            'iTotalRecords' => $productsCount,
            'iTotalDisplayRecords' => $productsCount,
            'aaData' => $rows
        );

        echo json_encode($json);
    }

    public function add(){
    
        return View('Admin.ProductUsage.add');
    
    }

    public function store(Request $request){
        
        $this->validate($request, [
            
            'name' => 'required|unique:App\Models\ProductUsage,name,',
            'status'   => 'required',
        ]);
        
        $productObj = new ProductUsage();
        $productObj->name = $request->name;
        $productObj->status = $request->status;
        if($productObj->save()){
            return redirect()->route('admin.product.usage')->with('success', 'ProductUsage added successfully.');
        } else {
            return redirect()->back()->with('error', 'ProductUsage not added.');
        }
    }

    public function edit($id){
       
        $productObj = ProductUsage::where('id', $id)->first();
       
        if(!empty($productObj)){
            return View('Admin.ProductUsage.edit', compact('productObj'));
        }
        else{
            return redirect()->back()->with('error', 'ProductUsage not found.');
        }
    }

    public function update($id, Request $request){
        $this->validate($request, [

            'name' => 'required|unique:App\Models\ProductUsage,name,'.$id,
            'status'   => 'required',
        ]);

        $productObj = ProductUsage::where('id', $id)->first();
        $productObj->name = $request->name;
        $productObj->status = $request->status;

        if($productObj->save()){ 
                return redirect()->route('admin.product.usage')->with('success', 'ProductUsage updated successfully.');
        } else {
                return redirect()->back()->with('error', 'ProductUsage not updated.');
        }
        
    }

    public function remove(Request $request){
        
        if($request->has('id')) {
            $productObj = ProductUsage::where('id', $request->id)->first();
            $productObj->delete();
            
        }
    }

    public function updateStatus(Request $request){
        if($request->has('id') && $request->has('status')) {
            $productObj = ProductUsage::where('id', $request->id)->first();
            if(!empty($productObj)){
                $productObj->status = $request->status;
                $productObj->save();
            }
        }
    }

    public function checkName(Request $request){
        $valid = TRUE;

        if(!empty($request->name)){
            $producttypeobj = ProductUsage::where('name', $request->name);
            if(!empty($request->id)){
                $producttypeobj = $producttypeobj->where('id', '!=', $request->id);    
            }
            $producttypeobj = $producttypeobj->count();
            if($producttypeobj){
                $valid = FALSE;
            }
        } else {
            $valid = FALSE;
        }

        return json_encode(array('valid' => $valid));
    }

}

