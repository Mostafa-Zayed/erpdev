<table class="table table-striped">
	<thead>
		<tr>
			<th>@lang('lang_v1.image')</th>
			<th>@lang('messages.action')</th>
		</tr>
	</thead>
	<tbody>
		@if(!empty($job_sheet->police_report))
                                                                @php
                                                           $title =    explode(',', $job_sheet->police_report);
                                                                    
                                                                   
                                                                    
                                                                @endphp
																	 @foreach($title as $key => $data1)
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $title[$key])}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $title[$key])}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.police_report')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $title[$key])}}" download="	{{__('garage::lang.police_report')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
				@endforeach
			
			@endif	
			
			
			@if(!empty($job_sheet->id_photo))
                                                                @php
                                                           $title =    explode(',', $job_sheet->id_photo);
                                                                    
                                                                   
                                                                    
                                                                @endphp
																	 @foreach($title as $key => $data1)
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $title[$key])}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $title[$key])}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.id_photo')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $title[$key])}}" download="	{{__('garage::lang.id_photo')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
				@endforeach
			
			@endif	
			
						
			@if(!empty($job_sheet->d_license))
                                                                @php
                                                           $title =    explode(',', $job_sheet->d_license);
                                                                    
                                                                   
                                                                    
                                                                @endphp
																	 @foreach($title as $key => $data1)
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $title[$key])}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $title[$key])}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.d_license')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $title[$key])}}" download="	{{__('garage::lang.d_license')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
				@endforeach
			
			@endif	
			
									
			@if(!empty($job_sheet->v_license))
                                                                @php
                                                           $title =    explode(',', $job_sheet->v_license);
                                                                    
                                                                   
                                                                    
                                                                @endphp
																	 @foreach($title as $key => $data1)
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $title[$key])}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $title[$key])}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.v_license')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $title[$key])}}" download="	{{__('garage::lang.v_license')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
				@endforeach
			
			@endif	
			
			
			
		
			
			
			@if(!empty($job_sheet->job_card_photo))
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $job_sheet->job_card_photo)}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->job_card_photo)}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.job_card_photo')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->job_card_photo)}}" download="	{{__('garage::lang.job_card_photo')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
			@endif	
			@if(!empty($job_sheet->estimation_photo))
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $job_sheet->estimation_photo)}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->estimation_photo)}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.estimation_photo')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->estimation_photo)}}" download="	{{__('garage::lang.estimation_photo')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
			@endif
		@if(!empty($job_sheet->lpo_photo))
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $job_sheet->lpo_photo)}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->lpo_photo)}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.lpo_photo')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->lpo_photo)}}" download="	{{__('garage::lang.lpo_photo')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
			@endif
		@if(!empty($job_sheet->receipt_photo))
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $job_sheet->receipt_photo)}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->receipt_photo)}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.receipt_photo')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->receipt_photo)}}" download="	{{__('garage::lang.receipt_photo')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
			@endif
		@if(!empty($job_sheet->invoice_photo))
			<tr class="media_row">
				<td>
				
				  <img src="{{   asset('/uploads/job_card/' . $job_sheet->invoice_photo)}}"style="width: 125px;;height: 86px;">
						<br>
				
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->invoice_photo)}}" class="cursor-pointer"target="_blank">
						{{__('garage::lang.invoice_photo')}}	
					</a>
				</td>
				<td>
					<a href="{{   asset('/uploads/job_card/' . $job_sheet->invoice_photo)}}" download="	{{__('garage::lang.invoice_photo')}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
				
				</td>
			</tr>
			@endif
	
	</tbody>
</table>