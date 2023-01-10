<?php

namespace App\Repositories;

use App\Interfaces\ProductInterface;
use App\Http\Traits\BusinessService;
use Illuminate\Support\Facades\Config;
use App\Http\Traits\Util;
use App\Utils\ModuleUtil;
use App\Http\Traits\ProductService;
use App\Product;
use App\Media;

class ProductRepository implements ProductInterface
{
    use BusinessService;
    use Util;
    use ProductService;
    
    protected $moduleUtil;
    
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;    
    }
    public function getAll()
    {
        
    }
    
    public function intialStoreData(& $request)
    {
        $product_details['business_id'] = $this->getBusinessId();
        
        $form_fields = Config::get('product.store_form_fields');
        
        $module_form_fields = $this->moduleUtil->getModuleFormField('product_form_fields');
            
        if (!empty($module_form_fields)) {
            
            $form_fields = array_merge($form_fields, $module_form_fields);
        }
        
        $product_details = $request->only($form_fields);
            
        $product_details['created_by'] = $this->getUser();
            
        $product_details['enable_stock'] = (!empty($request->input('enable_stock')) &&  $request->input('enable_stock') == 1) ? 1 : 0;
        
        $product_details['not_for_selling'] = (!empty($request->input('not_for_selling')) &&  $request->input('not_for_selling') == 1) ? 1 : 0;

            
        if (!empty($request->input('sub_category_id'))) {
            
            $product_details['sub_category_id'] = $request->input('sub_category_id') ;
        }

        if (empty($product_details['sku'])) {
            $product_details['sku'] = ' ';
        }
        
        $expiry_enabled = $this->getSessionValue('enable_product_expiry','business');
        
        if (!empty($request->input('expiry_period_type')) && !empty($request->input('expiry_period')) && !empty($expiry_enabled) && ($product_details['enable_stock'] == 1)) {
                $product_details['expiry_period_type'] = $request->input('expiry_period_type');
                $product_details['expiry_period'] = Util::num_uf($request->input('expiry_period'));
            }
        
        if (!empty($request->input('enable_sr_no')) &&  $request->input('enable_sr_no') == 1) {
                $product_details['enable_sr_no'] = 1 ;
            }   
        
        //upload document
        $product_details['image'] = Util::uploadFile($request, 'image', config('constants.product_img_path'), 'image');   
        
        $common_settings = $this->getSessionValue('common_settings','business');
        
        $product_details['warranty_id'] = !empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;
        
        return $product_details;
        dd($expiry_enabled,$product_details);
    }
    
    private function configExpirationDate()
    {
        
    }
    
    private function configSerialNumber()
    {
        
    }
    
    public function addProductVariation($productId,$sku,$data)
    {
        
    }
    
    public function createSingleVariation($product, $productData, $combo_variations = [])
    {
        if (!is_object($product)) {
            $product = Product::find($product);
        }
        
        //create product variations
        $product_variation_data = [
                                    'name' => 'DUMMY',
                                    'is_dummy' => 1
                                ];
        $product_variation = $product->product_variations()->create($product_variation_data);                        
        
        $variation = $product_variation->variations()->create($variation_data);

        Media::uploadMedia($product->business_id, $variation, request(), 'variation_images');

        return true;
    }
    
    public function createProductVariation($product, $productData,$combo = [])
    {
        
    }
}