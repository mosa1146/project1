<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title> طباعة  كشف حساب مورد </title>
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/bootstrap.min.css')}}">
      <style>
         @media print {    
         .hidden-print{display:none;}
         }
         td{font-size: 15px !important;text-align: center;}
      </style>
   <body style="padding-top: 10px;font-family: tahoma;">
      <table  cellspacing="0" style="width: 30%; margin-right: 5px; float: right;   "  dir="rtl">
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;"> كود المورد 
               <span style="margin-right: 10px;">/ {{ $data["suuplier_code"] }}</span>
            </td>
         </tr>
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;"> اسم المورد  <span style="margin-right: 10px;">/ {{ $data['name'] }}</span></td>
         </tr>
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;">  رقم التيلفون  <span style="margin-right: 10px;">/ {{ $data['phones'];}}</span></td>
         </tr>
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;">   تاريخ الاضافة  <span style="margin-right: 10px;">/ {{ $data['date'];}}</span></td>
         </tr>
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;">   
               <button class="btn btn-sm btn-primary hidden-print "  onclick="window.print()" >طباعة</button>
            </td>
         </tr>
      </table>
      <table style="width: 30%;float: right;  margin-right: 5px;" dir="rtl">
         <tr>
            <td style="text-align: center;padding: 5px;">  <span style=" display: inline-block;
               width: 200px;
               height: 30px;
               text-align: center;
               background: yellow !important;
               border: 1px solid black; border-radius: 15px;font-weight: bold;"> نوع التقرير </span></td>
         </tr>
         <tr>
            <td style="text-align: center;padding: 5px;font-weight: bold;">  <span >  
               تقرير  المشتريات خلال فترة <br> ({{ $data['from_date'] }} الي  {{   $data['to_date'] }})
               </span>
            </td>
         </tr>
         <tr>
            <td style="text-align: center;padding: 5px;font-weight: bold;"> 
               <span style=" display: inline-block;
                  width: 200px;
                  height: 30px;
                  text-align: center;
                  color: blue;
                  border: 1px solid black; "> 
               طبع بتاريخ @php echo date('Y-m-d'); @endphp
               </span>
            </td>
         </tr>
         <tr>
            <td style="text-align: center;padding: 5px;font-weight: bold;"> 
               <span style=" display: inline-block;
                  width: 200px;
                  height: 30px;
                  text-align: center;
                  color: blue;
                  border: 1px solid black; "> 
               طبع بواسطة {{ auth()->user()->name }}
               </span>
            </td>
         </tr>
      </table>
      <table style="width: 35%;float: right; margin-left: 5px; " dir="rtl">
         <tr>
            <td style="text-align:left !important;padding: 5px;">
               <img style="width: 150px; height: 110px; border-radius: 10px;" src="{{ asset('assets/admin/uploads').'/'.$systemData['photo'] }}"> 
               <p>{{ $systemData['system_name'] }}</p>
            </td>
         </tr>
      </table>
      <br>
      <table  dir="rtl" border="1" style="width: 98%; margin: 0 auto;"  id="example2" cellpadding="1" cellspacing="0"  aria-describedby="example2_info" >
         <tr>
            <td style="width: 25%; text-align: right; font-weight: bold">رقم الحساب المالي للمورد</td>
            <td style="width: 75%;text-align: right; padding-right: 5px; ">{{ $data['account_number'] }}</td>
         </tr>
         <tr>
            <td style="width: 25%; text-align: right; font-weight: bold">   المشتريات</td>
            <td style="width: 75%;text-align: right; padding-right: 5px; "> 
               عدد  ({{ $data['BurchaseCounter']*1 }}) فاتورة مشتريات بقيمة ({{ $data['BurchaseTotalMoney']*1 }}) جنيه
            </td>
         </tr>
         <tr>
            <td style="width: 25%; text-align: right; font-weight: bold">   رصيد المورد حاليا</td>
            <td style="width: 75%;text-align: right; padding-right: 5px; ">
               @if($data['the_final_Balance'] >0)
               مدين ب ({{ $data['the_final_Balance']*1 }}) جنيه  
               @elseif ($data['the_final_Balance'] <0)
               دائن ب ({{ $data['the_final_Balance']*1*(-1) }})   جنيه
               @else
               متزن
               @endif
            </td>
         </tr>
      </table>
      <h3 style="font-size: 16px; text-align: center; margin-top: 5px;font-weight: bold"> المشتريات من المورد خلال الفترة</h3>
      @if (@isset($details['Burchases']) && !@empty($details['Burchases']) && count($details['Burchases'])>0)
      @if($data['Does_show_items']==1)
      @foreach ($details['Burchases'] as $info )
      <table  dir="rtl" id="example2" class="table table-bordered table-hover" style="width: 99%;margin: 0 auto;">
         <thead style="background-color: lightgrey">
            <th>رقم الفاتورة</th>
            <th>تاريخ الفاتورة</th>
            <th> النوع</th>
            <th> اجمالي</th>
            <th> المدفوع </th>
            <th> المتبقي </th>
            <th> الحالة</th>
         </thead>
         <tbody>
            <tr>
               <td>{{ $info->auto_serial }}</td>
               <td>{{ $info->order_date }}</td>
               <td>@if($info->pill_type==1)  كاش  @elseif($info->pill_type==2)  اجل  @else  غير محدد @endif</td>
               <td>{{ $info->total_cost*1 }}</td>
               <td>{{ $info->what_paid*1 }}</td>
               <td>{{ $info->what_remain*1 }}</td>
               <td>@if($info->is_approved==1)  معتمدة   @else   مفتوحة @endif</td>
            </tr>
            <!---  هل طلبت عرض الاصناف-->
            @if($data['Does_show_items']==1)
            <tr>
               <td colspan="7">
                  @if (@isset($info->itemsdetails) && !@empty($info->itemsdetails) && count($info->itemsdetails)>0)
                  <table dir="rtl" id="example2" class="table table-bordered table-hover">
                     <thead  >
                        <th>الصنف </th>
                        <th> الوحده</th>
                        <th> الكمية</th>
                        <th> السعر</th>
                        <th> الاجمالي</th>
                     </thead>
                     <tbody>
                        @foreach ($info->itemsdetails as $det )
                        <tr>
                           <td>{{ $det->item_card_name }}
                           </td>
                           <td>{{ $det->uom_name }}</td>
                           <td>{{ $det->deliverd_quantity*(1) }}</td>
                           <td>{{ $det->unit_price*(1) }}</td>
                           <td>{{ $det->total_price*(1) }}</td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
                  @else
                  <div class="alert alert-danger">
                     عفوا لاتوجد بيانات لعرضها !!
                  </div>
                  @endif
               </td>
            </tr>
            @endif
         </tbody>
      </table>
      @endforeach
      @else
      <table  dir="rtl" id="example2" class="table table-bordered table-hover" style="width: 99%;margin: 0 auto;">
         <thead style="background-color: lightgrey">
            <th>رقم الفاتورة</th>
            <th>تاريخ الفاتورة</th>
            <th> النوع</th>
            <th> اجمالي</th>
            <th> المدفوع </th>
            <th> المتبقي </th>
            <th> الحالة</th>
         </thead>
         <tbody>
            @foreach ($details['Burchases'] as $info )
            <tr>
               <td>{{ $info->auto_serial }}</td>
               <td>{{ $info->order_date }}</td>
               <td>@if($info->pill_type==1)  كاش  @elseif($info->pill_type==2)  اجل  @else  غير محدد @endif</td>
               <td>{{ $info->total_cost*1 }}</td>
               <td>{{ $info->what_paid*1 }}</td>
               <td>{{ $info->what_remain*1 }}</td>
               <td>@if($info->is_approved==1)  معتمدة   @else   مفتوحة @endif</td>
            </tr>
            @endforeach
         </tbody>
      </table>
      @endif
      @else
      <div class="alert alert-danger">
         عفوا لاتوجد بيانات لعرضها !!
      </div>
      @endif
      <br>
      <br>
      <p style="
         padding: 10px 10px 0px 10px;
         bottom: 0;
         width: 100%;
         /* Height of the footer*/ 
         text-align: center;font-size: 16px; font-weight: bold;
         "> {{ $systemData['address'] }} - {{ $systemData['phone'] }} </p>
   </body>
</html>