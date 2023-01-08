<?php

namespace Modules\Garage\Utils;

use \Notification;
use App\Business;
use DB;
use App\NotificationTemplate;
use App\Charts\CommonChart;
use App\Utils\Util;
use Modules\Garage\Entities\GarageStatus;
use Spatie\Activitylog\Models\Activity;
use App\Transaction;
use App\Brands;
use Modules\Garage\Entities\DeviceModel;
use App\Category;
use Modules\Garage\Entities\JobSheet;
use Modules\Garage\Entities\JobCard;

use Modules\Garage\Notifications\GarageStatusUpdated;
use Modules\Garage\Entities\Company;
use Modules\Garage\Tests\OAuth;
use Modules\Garage\Tests\PHPMailer;
use Modules\Garage\Tests\SMTP;
use Modules\Garage\Tests\Exception;
use Modules\Garage\Tests\POP3;

class GarageUtil extends Util
{
    public function replaceModuleTags($business_id, $data, $job_sheet)
    {   
        $id = empty($job_sheet->garage_job_sheet_id) ? $job_sheet->id : $job_sheet->garage_job_sheet_id;

        $job_sheet = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id)
                        ->findOrFail($id);

        $business = Business::findOrFail($business_id);

        foreach ($data as $key => $value) {
            //replace customer name
            if (strpos($value, '{customer_name}') !== false) {
                $customer_name = optional($job_sheet->customer)->name;
                $data[$key] = str_replace('{customer_name}', $customer_name, $data[$key]);
            }

            //Replace job sheet number
            if (strpos($value, '{job_sheet_no}') !== false) {
                $job_sheet_no = $job_sheet->job_sheet_no;
                $data[$key] = str_replace('{job_sheet_no}', $job_sheet_no, $data[$key]);
            }

            //Replace status
            if (strpos($value, '{status}') !== false) {
                $data[$key] = str_replace('{status}', optional($job_sheet->status)->name, $data[$key]);
            }

            //Replace serial number
            if (strpos($value, '{serial_number}') !== false) {
                $serial_number = $job_sheet->serial_no;
                $data[$key] = str_replace('{serial_number}', $serial_number, $data[$key]);
            }

            //replace delivery_date
            if (strpos($value, '{delivery_date}') !== false) {
                $delivery_date = $job_sheet->completed_on;
                if (!empty($delivery_date)) {
                    $delivery_date = $this->format_date($delivery_date, true);
                }
                $data[$key] = str_replace('{delivery_date}', $delivery_date, $data[$key]);
            }

            //replace service staff name
            if (strpos($value, '{service_staff}') !== false && !empty($job_sheet->technician)) {
                $service_staff = optional($job_sheet->technician)->user_full_name;
                $data[$key] = str_replace('{service_staff}', $service_staff, $data[$key]);
            }

            //replace brand name
            if (strpos($value, '{brand}') !== false && !empty($job_sheet->Brand)) {
                $brand = optional($job_sheet->Brand)->name;
                $data[$key] = str_replace('{brand}', $brand, $data[$key]);
            }

            //replace device name
            if (strpos($value, '{device}') !== false && !empty($job_sheet->Device)) {
                $device = optional($job_sheet->company)->name;
                $data[$key] = str_replace('{device}', $device, $data[$key]);
            }

            //replace device model name
            if (strpos($value, '{device_model}') !== false && !empty($job_sheet->deviceModel)) {
                $device_model = $job_sheet->care_model;
                $data[$key] = str_replace('{device_model}', $device_model, $data[$key]);
            }

            //Replace business_name
            if (strpos($value, '{business_name}') !== false) {
                $business_name = $business->name;
                $data[$key] = str_replace('{business_name}', $business_name, $data[$key]);
            }
        }

        return $data;
    }
    
    
    public function replaceCompanyModuleTags($business_id, $data, $job_sheet)
    {   
        $id = empty($job_sheet->garage_job_sheet_id) ? $job_sheet->id : $job_sheet->garage_job_sheet_id;

        $job_sheet = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id)
                        ->findOrFail($id);

        $business = Business::findOrFail($business_id);

        foreach ($data as $key => $value) {
            //replace customer name
            if (strpos($value, '{company_name}') !== false) {
                $company_name = optional($job_sheet->company)->name;
                $data[$key] = str_replace('{company_name}', $company_name, $data[$key]);
            }

            //Replace job sheet number
            if (strpos($value, '{estimation_number}') !== false) {
                $job_sheet_no = $job_sheet->serial_no ;
                $data[$key] = str_replace('{estimation_number}', $job_sheet_no, $data[$key]);
            }

            //Replace serial number
            if (strpos($value, '{car_plate}') !== false) {
                $serial_number = $job_sheet->car_plate;
                $data[$key] = str_replace('{car_plate}', $serial_number, $data[$key]);
            } 
            if (strpos($value, '{car_brand}') !== false) {
                $brand = optional($job_sheet->brand)->name;
                $data[$key] = str_replace('{car_brand}', $brand, $data[$key]);
            }
            if (strpos($value, '{car_model}') !== false) {
                $care_model = $job_sheet->care_model;
                $data[$key] = str_replace('{car_model}', $care_model, $data[$key]);
            }

          
        }

        return $data;
    }

    public function repairWarrantyExpiresIn($repair)
    {
        $warranty = '';
        if (!empty($repair->repair_completed_on)) {
            $repair_completed_on = \Carbon::parse($repair->repair_completed_on);

            $warranty_expires_on = $repair_completed_on;
            if ($repair->duration_type == 'months') {
                $warranty_expires_on = $warranty_expires_on->addMonths($repair->duration);
            } elseif ($repair->duration_type == 'years') {
                $warranty_expires_on = $warranty_expires_on->addYears($repair->duration);
            } elseif ($repair->duration_type == 'days') {
                $warranty_expires_on = $warranty_expires_on->addDays($repair->duration);
            }

            $warranty =  $warranty_expires_on->diffForHumans();
        }
        return $warranty;
    }

    public function getRepairSettings($business_id)
    {
        $repair_settings = Business::where('id', $business_id)
                                ->value('garage_settings');

        $repair_settings = !empty($repair_settings) ? json_decode($repair_settings, true) : [];

        return $repair_settings;
    }

    public function sendRepairUpdateNotification($sms_body, $transaction)
    {
        $business_id = $transaction->business_id;
        $contact = $transaction->contact;
        $notification_data['sms_body'] = $sms_body;

        $tag_replaced_data = $this->replaceTags($business_id, $notification_data, $transaction);
        $tag_replaced_data = $this->replaceModuleTags($business_id, $tag_replaced_data, $transaction);

        $business = Business::findOrFail($business_id);
        $data['sms_settings'] = $business->sms_settings;
        $data['mobile_number'] = $contact->mobile;
        $data['sms_body'] = $tag_replaced_data['sms_body'];
        
        //Send sms
        if (!empty($contact->mobile) && !empty($data['sms_body'])) {
            $response = $this->sendSms($data);

            if (!empty($response) && $response->getStatusCode() == 200) {
                $is_sent = __('repair::lang.sms_sent');
            } else {
                $is_sent = __('repair::lang.sms_not_sent');
            }

            //loging if notification sent
            activity()
            ->performedOn($transaction)
            ->withProperties(['sms_body' => $data['sms_body'], 'mobile_number' => $data['mobile_number'], 'sent' => $is_sent])
            ->log('is_sent_notification');

            return $response;
        } else {
            return null;
        }
    }

    public function sendJobSheetUpdateSmsNotification($sms_body, $job_sheet)
    {
        $business_id = $job_sheet->business_id;
        $customer = $job_sheet->customer;
        $notification_data['sms_body'] = $sms_body;

        //replace tag from template
        $tag_replaced_data = $this->replaceModuleTags($business_id, $notification_data, $job_sheet);

        $business = Business::findOrFail($business_id);
        $data['sms_settings'] = $business->sms_settings;
        $data['mobile_number'] = $customer->mobile;
        $data['sms_body'] = $tag_replaced_data['sms_body'];

        //Send sms
        if (!empty($data['sms_settings']) && !empty($customer->mobile) && !empty($data['sms_body'])) {
            $response = $this->sendSms($data);
            if (!empty($response) && $response->getStatusCode() == 200) {
                $is_sent = __('repair::lang.sms_sent');
            } else {
                $is_sent = __('repair::lang.sms_not_sent');
            }

            //loging if notification sent
            activity()
            ->performedOn($job_sheet)
            ->withProperties(['sms_body' => $data['sms_body'], 'mobile_number' => $data['mobile_number'], 'sent' => $is_sent])
            ->log('is_sent_notification');

            return $response;
        } else {
            return null;
        }
    }

    public function sendJobSheetUpdateEmailNotification($notification_data, $job_sheet)
    {   
        $business_id = $job_sheet->business_id;
        $customer = $job_sheet->customer;

        //replace tag from template
        $tag_replaced_data = $this->replaceModuleTags($business_id, $notification_data, $job_sheet);

        if (!empty($customer->email)) {
            $customer->notify(new GarageStatusUpdated($tag_replaced_data));
        }
    }
  public function sendJobSheetUpdateEmailNotificationcompany($notification_data, $job_sheet)
    {   
        $business_id = $job_sheet->business_id;
        $customer = $job_sheet->customer;


        
        //replace tag from template
        $tag_replaced_data = $this->replaceCompanyModuleTags($business_id, $notification_data, $job_sheet);

        
         $status = Company::where('business_id', $business_id)
                            ->find($job_sheet->insurance_company_id);
                            
     //   dd($tag_replaced_data);
        if(!empty($status->emails)){
            
                $users_temp = explode(',', $status->emails);
            $this->sendemail($users_temp ,$tag_replaced_data['subject'] ,$tag_replaced_data['body'],$business_id,$job_sheet);
            
                        
          /*  Mail::send('emails.plain_html', array('content' => $tag_replaced_data['body']), 
                function($message) use ($users_temp, $tag_replaced_data) {
                    $message
                    ->from('admin@admin.org', 'Administrator')
                    ->subject($tag_replaced_data['subject']);
            
                    $message->to($users_temp);
            });
            */
            $users = [];
            
    /* $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=charset=utf-8\r\n";
    $headers .=   'X-Mailer: PHP/' . phpversion()."\r\n";
    $headers .= "From: ssssss\r\n";*/
  /*         foreach($users_temp as $key => $ut){
             
       $ua = [];

      $ua['email'] = $ut;

      $ua['name'] = 'test';

      $users[$key] = (object)$ua;
      //  mail($ut, $tag_replaced_data['subject'], $tag_replaced_data['body'], $headers);
        
          //     \Mail::to($ut)->send(new GarageStatusUpdated($tag_replaced_data)); 
            }*/

        }
    
        
        
     /*   if (!empty($customer->email)) {
            $customer->notify(new GarageStatusUpdated($tag_replaced_data));
        }*/
    }

    public function getRepairStatusTemplateTags()
    {
        return  [
            'tags' => ['{customer_name}', '{job_sheet_no}', '{status}', '{serial_number}', '{delivery_date}', '{service_staff}', '{brand}', '{device}', '{device_model}', '{business_name}'],
            'help_text' => __('lang_v1.available_tags')
        ];
    }
   public function getGaragetatusTemplateTags()
    {
        return  [
            'tags' => ['{company_name}', '{estimation_number}', '{car_plate}', '{car_brand}', '{car_model}'],
            'help_text' => __('lang_v1.available_tags')
        ];
    }

    public function getRepairByStatus($business_id)
    {
        $job_sheets_by_status = JobCard::join(
                    'garage_statuses as rs',
                    'garage_job_cards.status_id',
                    '=',
                    'rs.id'
                )
                ->where('garage_job_cards.business_id', $business_id)
                ->select(
                    DB::raw('COUNT(garage_job_cards.id) as total_job_sheets'),
                    'rs.name as status_name',
                    'rs.color',
                    'rs.sort_order'
                )
                ->groupBy('rs.id')
                ->orderBy('sort_order', 'asc')
                ->get();

        return $job_sheets_by_status;
    }

    public function getRepairByServiceStaff($business_id)
    {
        $job_sheets_by_service_staff = JobCard::leftJoin(
                        'users', 'garage_job_cards.service_staff',
                         '=',
                         'users.id'
                        )
                        ->where('garage_job_cards.business_id', $business_id)
                        ->whereNotNull('garage_job_cards.service_staff')
                        ->select(DB::raw("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as service_staff"),
                            DB::raw('COUNT(garage_job_cards.id) as total_job_sheets')
                        )
                        ->groupBy('garage_job_cards.service_staff')
                        ->get();
                        
        return $job_sheets_by_service_staff;
    }

    public function getTrendingRepairBrands($business_id)
    {
        $job_sheets = JobCard::leftJoin('brands',
                            'garage_job_cards.brand_id',
                            '=',
                            'brands.id')
                            ->where('garage_job_cards.business_id', $business_id)
                            ->whereNotNull('garage_job_cards.brand_id')
                            ->select('brands.name as brand',
                                DB::raw('COUNT(garage_job_cards.id) as job_sheets_brands')
                            )
                            ->limit(5)
                            ->groupBy('brands.id')
                            ->orderBy('job_sheets_brands', 'desc')
                            ->get();

        $labels = [];
        $values = [];
        foreach ($job_sheets as $key => $job_sheet) {
            $labels[] = $job_sheet['brand'];
            $values[] = $job_sheet['job_sheets_brands'];
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->options($this->__chartOptions(__('repair::lang.total_unit_repaired')))
            ->dataset(__('repair::lang.total_unit_repaired'), 'column', $values);

        return $chart;
    }

    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                    'title' => [
                        'text' => $title
                    ]
                ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical'
            ],
        ];
    }
    
    public function getTrendingDevices($business_id)
    {
        $job_sheets = JobCard::leftJoin('categories as CAT',
                            'garage_job_cards.device_id',
                            '=',
                            'CAT.id')
                            ->where('garage_job_cards.business_id', $business_id)
                            ->whereNotNull('garage_job_cards.device_id')
                            ->select('CAT.name as device',
                                DB::raw('COUNT(garage_job_cards.id) as job_sheet_devices')
                            )
                            ->limit(5)
                            ->groupBy('CAT.id')
                            ->orderBy('job_sheet_devices', 'desc')
                            ->get();

        $labels = [];
        $values = [];
        foreach ($job_sheets as $key => $job_sheet) {
            $labels[] = $job_sheet['device'];
            $values[] = $job_sheet['job_sheet_devices'];
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->options($this->__chartOptions(__('repair::lang.total_unit_repaired')))
            ->dataset(__('repair::lang.total_unit_repaired'), 'column', $values);

        return $chart;
    }

    public function getTrendingDeviceModels($business_id)
    {
        $job_sheets = JobCard::leftJoin('repair_device_models as RDM',
                            'garage_job_cards.device_model_id',
                            '=',
                            'RDM.id')
                            ->leftJoin('car_brands', 'RDM.brand_id',
                            '=', 'brands.id')
                            ->leftJoin('categories as CAT',
                            'RDM.device_id',
                            '=',
                            'CAT.id')
                            ->where('garage_job_cards.business_id', $business_id)
                            ->whereNotNull('garage_job_cards.device_model_id')
                            ->select('RDM.name as device_model', 'brands.name as brand',
                                DB::raw('COUNT(garage_job_cards.id) as job_sheet_models'),
                                'CAT.name as device'
                            )
                            ->limit(5)
                            ->groupBy('RDM.id')
                            ->orderBy('job_sheet_models', 'desc')
                            ->get();

        $labels = [];
        $values = [];
        foreach ($job_sheets as $key => $job_sheet) {
            $label = $job_sheet['device_model'];
            $brand = $job_sheet['brand'];
            $device = $job_sheet['device'];
            if (!empty($brand) && !empty($device)) {
                $label = $job_sheet['device_model'] .' ('.$brand.' / '.$device.')';
            } elseif (!empty($brand)) {
                $label = $job_sheet['device_model'] .' ('.$brand.')';
            } elseif (!empty($device)) {
                $label = $job_sheet['device_model'] .' ('.$device.')';
            }
            $labels[] = $label;
            $values[] = $job_sheet['job_sheet_models'];
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->options($this->__chartOptions(__('repair::lang.total_unit_repaired')))
            ->dataset(__('repair::lang.total_unit_repaired'), 'column', $values);

        return $chart;
    }
    
    
     public function sendemail($emails , $subject ,$message,$business_id,$job_sheet){
         
         $mail = new PHPMailer(true);

 try {
     
     
     
         $business = Business::findOrFail($business_id);
     
     
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
  //  $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'mostafahamdi235@gmail.com';                     //SMTP username
    $mail->Password   = 'jqloohhvutagmhyi';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($business->owner->email, $business->name);
 //   $mail->addAddress('ffplsinfo@rowad.eu');     //Add a recipient
 
 if(optional($job_sheet->company)->to_email){
     
      $mail->addAddress(optional($job_sheet->company)->to_email);               //Name is optional 
 }
 
   
  //  $mail->addReplyTo('ffplsinfo@rowad.eu', 'Information');
  
  foreach($emails as $email){
      
       $mail->addCC($email);  
  }
 


    //Attachments
    $mail->addAttachment(public_path('uploads/job_card/'.$job_sheet->estimation_pdf));         //Add attachments
    
    
    
    
    	if(!empty($job_sheet->police_report)){
    	     $title =    explode(',', $job_sheet->police_report);
          foreach($title as $key => $data1){
             
             $mail->addAttachment(public_path('uploads/job_card/'.$title[$key]), 'police_report');    //Optional name  
         }
   	}
   	
   		if(!empty($job_sheet->id_photo)){
    	     $title =    explode(',', $job_sheet->id_photo);
          foreach($title as $key => $data1){
             
             $mail->addAttachment(public_path('uploads/job_card/'.$title[$key]), 'id_photo');    //Optional name  
         }
   	}
   	
   		if(!empty($job_sheet->d_license)){
    	     $title =    explode(',', $job_sheet->d_license);
          foreach($title as $key => $data1){
             
             $mail->addAttachment(public_path('uploads/job_card/'.$title[$key]), 'd_license');    //Optional name  
         }
   	}
   	
   		if(!empty($job_sheet->v_license)){
    	     $title =    explode(',', $job_sheet->v_license);
          foreach($title as $key => $data1){
             
             $mail->addAttachment(public_path('uploads/job_card/'.$title[$key]), 'v_license');    //Optional name  
         }
   	}
   	
   	
   	
    /* $mail->addAttachment(public_path('uploads/job_card/'.$job_sheet->id_photo), 'id_photo');    //Optional name
     $mail->addAttachment(public_path('uploads/job_card/'.$job_sheet->d_license), 'd_license');    //Optional name
     $mail->addAttachment(public_path('uploads/job_card/'.$job_sheet->v_license), 'v_license');    //Optional name
     */
     
     
    if($job_sheet->media->count() > 0){
        
        foreach($job_sheet->media as $media){
            
      $mail->addAttachment(public_path('uploads/media/'.$media->file_name));         //Add attachments   
            
        }
      
    }
   
   
 

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;
  //  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
  //  echo 'Message has been sent';
 
    
} catch (Exception $e) {
    
     \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
  //  echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

         
         
     }
    
}
