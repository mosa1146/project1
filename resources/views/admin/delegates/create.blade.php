@extends('layouts.admin')
@section('title')
المناديب
@endsection

@section('contentheader')
الحسابات  
@endsection

@section('contentheaderlink')
<a href="{{ route('admin.accounts.index') }}">    المناديب </a>
@endsection

@section('contentheaderactive')
عرض
@endsection

@section('content')



      <div class="card">
        <div class="card-header">
          <h3 class="card-title card_title_center"> اضافة حساب مندوب جديد</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
       
      
      <form action="{{ route('admin.delegates.store') }}" method="post" >
        <div class="row">
        @csrf
    
<div class="col-md-6">   
<div class="form-group">
  <label>اسم   المندوب</label>
  <input name="name" id="name" class="form-control" value="{{ old('name') }}"    >
  @error('name')
  <span class="text-danger">{{ $message }}</span>
  @enderror
</div>
</div>
   
      <div class="col-md-6">   
        <div class="form-group">
          <label>   حالة رصيد اول المدة</label>
          <select name="start_balance_status" id="start_balance_status" class="form-control">
            <option value="">اختر الحالة</option>
           <option   @if(old('start_balance_status')==1) selected="selected"  @endif value="1"> دائن</option>
           <option   @if(old('start_balance_status')==2) selected="selected"  @endif value="2"> مدين</option>
           <option   @if(old('start_balance_status')==3) selected="selected"  @endif value="3"> متزن</option>
           </select>
          @error('start_balance_status')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>
        </div>
        <div class="col-md-6">   
          <div class="form-group">
            <label>   رصيد أول المدة للحساب</label>
            <input  name="start_balance" id="start_balance" class="form-control"  oninput="this.value=this.value.replace(/[^0-9.]/g,'');" value="{{ old('start_balance') }}"    >
            @error('start_balance')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group"> 
              <label>   نوع عمولة  المندوب </label>
              <select name="percent_type" id="percent_type" class="form-control">
               <option value="">اختر الحالة</option>
              <option   @if(old('percent_type')==1  ) selected="selected"  @endif value="1"> اجر ثابت</option>
               <option @if( old('percent_type')==0 and   old('percent_type')!="") selected="selected"  @endif   value="2"> نسبة</option>
              </select>
              @error('percent_type')
              <span class="text-danger">{{ $message }}</span>
              @enderror
              </div>
            </div>

            <div class="col-md-6">   
              <div class="form-group">
                <label>    عمولة المندوب بالمبيعات قطاعي	</label>
                <input  name="percent_salaes_commission_kataei" id="percent_salaes_commission_kataei" class="form-control"  oninput="this.value=this.value.replace(/[^0-9.]/g,'');" value="{{ old('start_balance') }}"    >
                @error('percent_salaes_commission_kataei')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
              </div>

              <div class="col-md-6">   
                <div class="form-group">
                  <label>  عمولة المندوب بمبيعات نص الجملة	</label>
                  <input  name="percent_salaes_commission_nosjomla" id="percent_salaes_commission_nosjomla" class="form-control"  oninput="this.value=this.value.replace(/[^0-9.]/g,'');" value="{{ old('start_balance') }}"    >
                  @error('percent_salaes_commission_nosjomla')
                  <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
                </div>

                <div class="col-md-6">   
                  <div class="form-group">
                    <label>   عمولة المندوب بمبيعات الجملة	</label>
                    <input  name="percent_salaes_commission_jomla" id="percent_salaes_commission_jomla" class="form-control"  oninput="this.value=this.value.replace(/[^0-9.]/g,'');" value="{{ old('start_balance') }}"    >
                    @error('percent_salaes_commission_jomla')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                  </div>

                  <div class="col-md-6">   
                    <div class="form-group">
                      <label>   عمولة المندوب  بتحصيل الآجل	</label>
                      <input  name="percent_collect_commission" id="percent_collect_commission" class="form-control"  oninput="this.value=this.value.replace(/[^0-9.]/g,'');" value="{{ old('start_balance') }}"    >
                      @error('percent_collect_commission')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>
                    </div>

          <div class="col-md-6">   
            <div class="form-group">
              <label>   العنوان </label>
              <input name="address" id="name" class="form-control" value="{{ old('address') }}"    >
              @error('address')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            </div>
            <div class="col-md-6">   
              <div class="form-group">
                <label>   الهاتف</label>
                <input name="phones" id="phones" class="form-control" value="{{ old('phones') }}"    >
                @error('phones')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
              </div>
          <div class="col-md-6">   
            <div class="form-group">
              <label>   ملاحظات</label>
              <input name="notes" id="notes" class="form-control" value="{{ old('notes') }}"    >
              @error('notes')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            </div>
         
<div class="col-md-6">
      <div class="form-group"> 
        <label>  حالة التفعيل</label>
        <select name="active" id="active" class="form-control">
         <option value="">اختر الحالة</option>
        <option   @if(old('active')==1  || old('active')=="" ) selected="selected"  @endif value="1"> نعم</option>
         <option @if( (old('active')==0 and old('active')!="")) selected="selected"  @endif   value="0"> لا</option>
        </select>
        @error('active')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div>
      </div>
     
      
      <div class="col-md-12">
      <div class="form-group text-center">
        <button id="do_add_item_cardd" type="submit" class="btn btn-primary btn-sm"> اضافة</button>
        <a href="{{ route('admin.delegates.index') }}" class="btn btn-sm btn-danger">الغاء</a>    
      
      </div>
    </div>
    
  </div>  
            </form>  
        
            

            </div>  

      


        </div>
      </div>
   





@endsection


@section('script')
<script src="{{ asset('assets/admin/js/customers.js') }}"></script>
@endsection






