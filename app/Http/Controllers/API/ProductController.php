<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\User;
use App\Http\Resources\Product as ProductResource;
use App\Http\Controllers\Traits\MediaUploadingTrait;

class ProductController extends BaseController
{    
    use MediaUploadingTrait;

    public function index()
    {
        $products = auth()->user()->products;
        return $this->sendResponse(ProductResource::collection($products), 'Posts fetched.');
    }

    
    public function store(Request $request)
    {
        $message = [
            'photos.required'       => '画像: 未入力項目があります。',
            'description.required'        => '商品説明: 未入力項目があります。',
            'time.required'  => '時刻: 未入力項目があります。',
            'station.required'        => '駅: 未入力項目があります。',
       ];
        $validator = Validator::make($request->all(), [
            'photos' => ['required'],
            'description' => ['required'],
            'time' => ['required', 'numeric'],
            'station' => ['required', 'numeric'],
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }
        $data = $request->except('_token');
        $data['user_id'] = auth()->user()->id;
        $product = Product::create($data);
        $path = storage_path('tmp/uploads/');
        
        foreach ($request->input('photos', []) as $file) {
            $product->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('photos');
        }

        return $this->sendResponse(new ProductResource($product), '商品が追加されました。');
    }

   
    public function show($id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->sendError(config('myconfig.no_product'));
        }
        return $this->sendResponse(new ProductResource($product), 'Post fetched.');
    }

    public function edit($id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->sendError(config('myconfig.no_product'));
        }
        return $this->sendResponse(new ProductResource($product), 'Post fetched.');
    }
    

    public function update(Request $request, Product $product)
    {
        $message = [
            'photos.required'       => '画像: 未入力項目があります。',
            'description.required'        => '商品説明: 未入力項目があります。',
            'time.required'  => '時刻: 未入力項目があります。',
            'station.required'        => '駅: 未入力項目があります。',
       ];
        $validator = Validator::make($request->all(), [
            'photos' => ['required'],
            'description' => ['required'],
            'time' => ['required', 'numeric'],
            'station' => ['required', 'numeric'],
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $data = $request->except('_token');
        $product->update($data);

        $path = storage_path('tmp/uploads/');
        if (count($product->photos) > 0) {
            foreach ($product->photos as $media) {
                if (!in_array($media->file_name, $request->input('photos', []))) {
                    $media->delete();
                }
            }
        }
        $media = $product->photos->pluck('file_name')->toArray();
        foreach ($request->input('photos', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $product->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('photos');
            }
        }
        
        return $this->sendResponse(new ProductResource($product), config('myconfig.update_message'));
    }
   
    public function destroy(Product $product)
    {
        if (count($product->photos) > 0) {
            foreach ($product->photos as $media) {
                $media->delete();
            }
        }
        $product->delete();
        return $this->sendResponse([], config('myconfig.delete_message'));
    }
    public function total()
    {
        $products = Product::latest()->get();
        return $this->sendResponse(ProductResource::collection($products), 'Posts fetched.');
    }
}