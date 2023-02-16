<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title> طباعة فاتورة مشتريات </title>
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      
      <style>
          #pA6{
   margin-top: 3px !important; width: 100%;float:right
 }
  td{font-size: 15px !important;text-align: center;}
 .mainheadtable{
   width: 30%; margin-right: 5px; float: right;  border: 1px dashed black 
 }
 .tdhead{
   padding: 3px; text-align: right;font-weight: bold;
 }
 .mainheadtable2{
   width: 30%;float: right;  margin-right: 5px;
 }
 .headimg{
   width: 35%;float: right; margin-left: 5px;
 }
 .headimg_img{
   width: 150px; height: 110px; border-radius: 10px;
 }
  @media print {        
@page {
  size: 105mm 148mm;
}
td{font-size: 9px !important;text-align: center;} 
table{margin: 0 auto;}   
.mainheadtable{
   width: 50%; margin-right: 1px; float: right;  border: 1 solid black 
 }
 .tdhead{
   padding: 3px; text-align: right;font-weight: bold;
 }
 .tableDetails{
   margin-top: 5px !important;
 }
 .headimg{
   width: 45%;float: right; margin-left: 1px;
 }
 .headimg_img{
   width: 70px; height: 70px; float: left;
 }
 #pA6{
   margin-top: 3px !important; width: 100%;
 }
}

 
      </style>

   <body style="padding-top: 10px;font-family: tahoma;">
      <div class="row">
      <table class="mainheadtable"  cellspacing="0"   dir="rtl">
        
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;">  نوع الفاتورة 
               <span style="margin-right: 10px;">/ @if($data["order_type"] ==1) خدمات مقدمة لنا @else خدمات نقدمها للغير @endif</span>
           
            </td>
         </tr>
         @if($data["is_account_number"] ==1)
        
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;"> كود الحساب المالي 
               <span style="margin-right: 10px;">/ {{ $data["account_number"] }}</span>
           
            </td>
         </tr>
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;"> اسم الحساب المالي  <span style="margin-right: 10px;">/ {{ $data['account_name'] }}</span></td>
         </tr>
       @else
       <tr>
         <td style="padding: 5px; text-align: right;font-weight: bold;"> اسم الجهة الخارجية   <span style="margin-right: 10px;">/ {{ $data['entity_name'] }}</span></td>
      </tr>
       @endif


         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;">   تاريخ الفاتورة  <span style="margin-right: 10px;">/ {{ $data['order_date']}}</span></td>
         </tr>
       
         <tr>
            <td style="padding: 5px; text-align: right;font-weight: bold;">   حالة الفاتورة  <span style="margin-right: 10px;">/ @if($data['is_approved']==1) معتمدة @else غير معتمدة @endif</span></td>
         </tr>
      </table>
      <br>

      <table class="headimg"  dir="rtl" style="margin-bottom: 5px;">
         <tr>
            <td style="text-align:left !important;padding: 5px;">
               <img class="headimg_img"  src="{{ asset('assets/admin/uploads').'/'.$systemData['photo'] }}"> 
               <p>{{ $systemData['system_name'] }}</p>
            </td>
         </tr>
      </table>
      </div>
      <div class="clearfix" style="width:100%;"></div> 

         <p id="pA6"></p>
 
      
      <table  class="tableDetails" dir="rtl" border="1" style="width: 98%;   auto;"  id="example2" cellpadding="1" cellspacing="0"  aria-describedby="example2_info" >
      
         <tr style="background-color: gainsboro">
            <td style="font-weight: bold;">م</td>
            <td  style="font-weight: bold;">الخدمة</td>
            <td style="font-weight: bold;">اجمالي</td>
            <td style="font-weight: bold;">ملاحظات</td>

         </tr>
         @if(!@empty($invoices_details) and count($invoices_details)>0)
         @php $i=1;  @endphp
         @foreach($invoices_details as $info)
         <tr>
            <td>
               {{ $i }}
            </td>
           
            <td>
               {{ $info->service_name }}
           
            </td>
            <td>
               {{$info->total*1  }}
            </td>
         
           
            <td>
               {{$info->notes }}                                  
            </td>
         
         </tr>
         <?php $i++; endforeach;?>
         <tr>
            <td colspan="4" style="color:brown !important"><br>  اجمالي الخدمات  
               <?=$data['total_services']*1?> جنيه فقط لاغير 
            </td>
         </tr>
         @endif
      </table>
      
      <br>
      <table  dir="rtl" border="1" style="width: 98%; margin: 0 auto;"  id="example2" cellpadding="1" cellspacing="0"  aria-describedby="example2_info" >
         <tr >
            <td style="font-weight: bold;">اجمالي الفاتورة</td>
            <td style="font-weight: bold;">خصم</td>
            <td style="font-weight: bold;">قيمة مضافة</td>
            <td style="font-weight: bold;">صافي الفاتورة </td>
            <td style="font-weight: bold;">مدفوع</td>
            <td  style="font-weight: bold;">متبقي</td>
         </tr>
         <tr>
            <td>{{ $data["total_befor_discount"]*(1)}}</td>
            <td>{{$data['discount_value']*(1)}}</td>
            <td>{{$data['tax_value']*(1)}}</td>
            <td>{{$data['total_cost']*(1)}}</td>
            <td>{{$data['what_paid']*(1)}}</td>
            <td>{{$data['what_remain']*(1)}}</td>
         </tr>
      </table>
      <p style="position: fixed;
         padding: 10px 10px 0px 10px;
         bottom: 0;
         width: 100%;
         /* Height of the footer*/ 
         text-align: center;font-size: 11px; font-weight: bold;
         "> {{ $systemData['address'] }} - {{ $systemData['phone'] }} </p>
      <script>
         window.print();
           
      </script> 
   </body>
</html>