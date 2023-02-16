<?php
namespace App\Http\Controllers\Admin;
use App\Models\Admin;
use App\Models\Suppliers_with_orders;
use App\Models\Suppliers_with_orders_details;
use App\Models\Inv_itemCard;
use App\Models\Inv_uom;
use App\Models\Store;
use App\Models\Admins_Shifts;
use App\Models\Treasuries;
use App\Models\Treasuries_transactions;
use App\Models\Inv_itemcard_movements;
use App\Models\Account;
use App\Models\Supplier;
use App\Models\Admin_panel_setting;
use App\Models\Inv_itemcard_batches;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Suppliers_with_ordersRequest;
use App\Http\Requests\SupplierWithOrdersApproveBursahseRequst;
use App\Models\services_with_orders;

class Suppliers_with_ordersController extends Controller
{
public function index()
{
$com_code = auth()->user()->com_code;
$data = get_cols_where_p(new Suppliers_with_orders(), array("*"), array("com_code" => $com_code,'order_type'=>1), 'id', 'DESC', PAGINATION_COUNT);
if (!empty($data)) {
foreach ($data as $info) {
$info->added_by_admin = Admin::where('id', $info->added_by)->value('name');
$info->supplier_name = Supplier::where('suuplier_code', $info->suuplier_code)->value('name');
$info->store_name = Store::where('id', $info->store_id)->value('name');
if ($info->updated_by > 0 and $info->updated_by != null) {
$info->updated_by_admin = Admin::where('id', $info->updated_by)->value('name');
}
}
}
$suupliers = get_cols_where(new Supplier(), array('suuplier_code', 'name'), array('com_code' => $com_code), 'id', 'DESC');
$stores = get_cols_where(new Store(), array('id', 'name'), array('com_code' => $com_code, 'active' => 1), 'id', 'DESC');
return view('admin.suppliers_with_orders.index', ['data' => $data, 'suupliers' => $suupliers, 'stores' => $stores]);
}
public function create()
{
$com_code = auth()->user()->com_code;
$suupliers = get_cols_where(new Supplier(), array('suuplier_code', 'name'), array('com_code' => $com_code, 'active' => 1), 'id', 'DESC');
$stores = get_cols_where(new Store(), array('id', 'name'), array('com_code' => $com_code, 'active' => 1), 'id', 'DESC');
return view('admin.suppliers_with_orders.create', ['suupliers' => $suupliers, 'stores' => $stores]);
}
public function store(Suppliers_with_ordersRequest $request)
{
try {
$com_code = auth()->user()->com_code;
$supplierData = get_cols_where_row(new Supplier(), array("account_number"), array("suuplier_code" => $request->suuplier_code, "com_code" => $com_code));
if (empty($supplierData)) {
return redirect()->back()
->with(['error' => 'عفوا   غير قادر علي الوصول الي بيانات المورد المحدد'])
->withInput();
}
$row = get_cols_where_row_orderby(new Suppliers_with_orders(), array("auto_serial"), array("com_code" => $com_code,'order_type'=>1), 'id', 'DESC');
if (!empty($row)) {
$data_insert['auto_serial'] = $row['auto_serial'] + 1;
} else {
$data_insert['auto_serial'] = 1;
}
$data_insert['order_date'] = $request->order_date;
$data_insert['order_type'] = 1;
$data_insert['DOC_NO'] = $request->DOC_NO;
$data_insert['suuplier_code'] = $request->suuplier_code;
$data_insert['pill_type'] = $request->pill_type;
$data_insert['store_id'] = $request->store_id;
$data_insert['account_number'] = $supplierData['account_number'];
$data_insert['added_by'] = auth()->user()->id;
$data_insert['created_at'] = date("Y-m-d H:i:s");
$data_insert['date'] = date("Y-m-d");
$data_insert['com_code'] = $com_code;
Suppliers_with_orders::create($data_insert);
$id = get_field_value(new Suppliers_with_orders(), "id", array("auto_serial" => $data_insert['auto_serial'], "com_code" => $com_code, "order_type" => 1));
return redirect()->route("admin.suppliers_orders.show", $id)->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
} catch (\Exception $ex) {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
->withInput();
}
}


public function show($id)
{
try {
$com_code = auth()->user()->com_code;
$data = get_cols_where_row(new Suppliers_with_orders(), array("*"), array("id" => $id, "com_code" => $com_code, 'order_type' => 1));
if (empty($data)) {
return redirect()->route('admin.suppliers_orders.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
}
$data['added_by_admin'] = Admin::where('id', $data['added_by'])->value('name');
$data['supplier_name'] = Supplier::where('suuplier_code', $data['suuplier_code'])->value('name');
$data['store_name'] = Store::where('id', $data['store_id'])->value('name');
if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
$data['updated_by_admin'] = Admin::where('id', $data['updated_by'])->value('name');
}
$details = get_cols_where(new Suppliers_with_orders_details(), array("*"), array('suppliers_with_orders_auto_serial' => $data['auto_serial'], 'order_type' => 1, 'com_code' => $com_code), 'id', 'DESC');
if (!empty($details)) {
foreach ($details as $info) {
$info->item_card_name = Inv_itemCard::where('item_code', $info->item_code)->value('name');
$info->uom_name = get_field_value(new Inv_uom(), "name", array("id" => $info->uom_id));
$data['added_by_admin'] = Admin::where('id', $data['added_by'])->value('name');
if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
$data['updated_by_admin'] = Admin::where('id', $data['updated_by'])->value('name');
}
}
}
return view("admin.suppliers_with_orders.show", ['data' => $data, 'details' => $details]);
} catch (\Exception $ex) {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()]);
}
}

public function edit($id)
{
$com_code = auth()->user()->com_code;
$data = get_cols_where_row(new Suppliers_with_orders(), array("*"), array("id" => $id, "com_code" => $com_code, 'order_type' => 1));
if (empty($data)) {
return redirect()->route('admin.suppliers_orders.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
}
if ($data['is_approved'] == 1) {
return redirect()->route('admin.suppliers_orders.index')->with(['error' => 'عفوا لايمكن التحديث علي فاتورة معتمدة ومؤرشفة']);
}
$suupliers = get_cols_where(new Supplier(), array('suuplier_code', 'name'), array('com_code' => $com_code, 'active' => 1), 'id', 'DESC');
$stores = get_cols_where(new Store(), array('id', 'name'), array('com_code' => $com_code, 'active' => 1), 'id', 'DESC');
return view('admin.suppliers_with_orders.edit', ['data' => $data, 'suupliers' => $suupliers, 'stores' => $stores]);
}
public function update($id, Suppliers_with_ordersRequest $request)
{
try {
$com_code = auth()->user()->com_code;
$data = get_cols_where_row(new Suppliers_with_orders(), array("is_approved"), array("id" => $id, "com_code" => $com_code, 'order_type' => 1));
if (empty($data)) {
return redirect()->route('admin.suppliers_with_orders.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
}
$supplierData = get_cols_where_row(new Supplier(), array("account_number"), array("suuplier_code" => $request->suuplier_code, "com_code" => $com_code));
if (empty($supplierData)) {
return redirect()->back()
->with(['error' => 'عفوا   غير قادر علي الوصول الي بيانات المورد المحدد'])
->withInput();
}
$data_to_update['order_date'] = $request->order_date;
$data_to_update['order_type'] = 1;
$data_to_update['DOC_NO'] = $request->DOC_NO;
$data_to_update['suuplier_code'] = $request->suuplier_code;
$data_to_update['pill_type'] = $request->pill_type;
$data_to_update['store_id'] = $request->store_id;
$data_to_update['account_number'] = $supplierData['account_number'];
$data_to_update['updated_by'] = auth()->user()->id;
$data_to_update['updated_at'] = date("Y-m-d H:i:s");
update(new Suppliers_with_orders(), $data_to_update, array("id" => $id, "com_code" => $com_code, 'order_type' => 1));
return redirect()->route('admin.suppliers_orders.show', $id)->with(['success' => 'لقد تم تحديث البيانات بنجاح']);
} catch (\Exception $ex) {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
->withInput();
}
}
public function get_item_uoms(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
$item_code = $request->item_code;
$item_card_Data = get_cols_where_row(new Inv_itemCard(), array("does_has_retailunit", "retail_uom_id", "uom_id"), array("item_code" => $item_code, "com_code" => $com_code));
if (!empty($item_card_Data)) {
if ($item_card_Data['does_has_retailunit'] == 1) {
$item_card_Data['parent_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['uom_id']));
$item_card_Data['retial_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['retail_uom_id']));
} else {
$item_card_Data['parent_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['uom_id']));
}
}
return view("admin.suppliers_with_orders.get_item_uoms", ['item_card_Data' => $item_card_Data]);
}
}
public function add_new_details(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
$item_code = $request->item_code;
$suppliers_with_ordersData = get_cols_where_row(new Suppliers_with_orders(), array("is_approved", "order_date", "tax_value", "discount_value","id"), array("auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
if (!empty($suppliers_with_ordersData)) {
if ($suppliers_with_ordersData['is_approved'] == 0) {
$data_insert['suppliers_with_orders_auto_serial'] = $request->autoserailparent;
$data_insert['order_type'] = 1;
$data_insert['suppliers_with_order_id'] = $suppliers_with_ordersData['id'];
$data_insert['item_code'] = $request->item_code_add;
$data_insert['deliverd_quantity'] = $request->quantity_add;
$data_insert['unit_price'] = $request->price_add;
$data_insert['uom_id'] = $request->uom_id_Add;
$data_insert['isparentuom'] = $request->isparentuom;
if ($request->type == 2) {
$data_insert['production_date'] = $request->production_date;
$data_insert['expire_date'] = $request->expire_date;
}
$data_insert['item_card_type'] = $request->type;
$data_insert['total_price'] = $request->total_add;
$data_insert['order_date'] = $suppliers_with_ordersData['order_date'];
$data_insert['added_by'] = auth()->user()->id;
$data_insert['created_at'] = date("Y-m-d H:i:s");
$data_insert['com_code'] = $com_code;
$flag = insert(new Suppliers_with_orders_details(), $data_insert);
if ($flag) {
/** update parent pill */
$total_detials_sum = get_sum_where(new Suppliers_with_orders_details(), 'total_price', array("suppliers_with_orders_auto_serial" => $request->autoserailparent, 'order_type' => 1, 'com_code' => $com_code));
$dataUpdateParent['total_cost_items'] = $total_detials_sum;
$dataUpdateParent['total_befor_discount'] = $total_detials_sum + $suppliers_with_ordersData['tax_value'];
$dataUpdateParent['total_cost'] = $dataUpdateParent['total_befor_discount'] - $suppliers_with_ordersData['discount_value'];
$dataUpdateParent['updated_by'] = auth()->user()->id;
$dataUpdateParent['updated_at'] = date("Y-m-d H:i:s");
update(new Suppliers_with_orders(), $dataUpdateParent, array("auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
echo json_encode("done");
}
}
}
}

}
public function reload_itemsdetials(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
$auto_serial = $request->autoserailparent;
$data = get_cols_where_row(new Suppliers_with_orders(), array("is_approved","id"), array("auto_serial" => $auto_serial, "com_code" => $com_code, 'order_type' => 1));
if (!empty($data)) {
$details = get_cols_where(new Suppliers_with_orders_details(), array("*"), array('suppliers_with_orders_auto_serial' => $auto_serial, 'order_type' => 1, 'com_code' => $com_code), 'id', 'DESC');
if (!empty($details)) {
foreach ($details as $info) {
$info->item_card_name = Inv_itemCard::where('item_code', $info->item_code)->value('name');
$info->uom_name = get_field_value(new Inv_uom(), "name", array("id" => $info->uom_id));
$data['added_by_admin'] = Admin::where('id', $data['added_by'])->value('name');
if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
$data['updated_by_admin'] = Admin::where('id', $data['updated_by'])->value('name');
}
}
}
}
return view("admin.suppliers_with_orders.reload_itemsdetials", ['data' => $data, 'details' => $details]);
}
}
public function reload_parent_pill(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
$data = get_cols_where_row(new Suppliers_with_orders(), array("*"), array("auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
if (!empty($data)) {
$data['added_by_admin'] = Admin::where('id', $data['added_by'])->value('name');
$data['supplier_name'] = Supplier::where('suuplier_code', $data['suuplier_code'])->value('name');
if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
$data['updated_by_admin'] = Admin::where('id', $data['updated_by'])->value('name');
}
return view("admin.suppliers_with_orders.reload_parent_pill", ['data' => $data]);
}
}
}
public function load_edit_item_details(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
$parent_pill_data = get_cols_where_row(new Suppliers_with_orders(), array("is_approved"), array("auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
if (!empty($parent_pill_data)) {
if ($parent_pill_data['is_approved'] == 0) {
$item_data_detials = get_cols_where_row(new Suppliers_with_orders_details(), array("*"), array("suppliers_with_orders_auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1, 'id' => $request->id));
$item_cards = get_cols_where(new Inv_itemCard(), array("name", "item_code", "item_type"), array('active' => 1, 'com_code' => $com_code), 'id', 'DESC');
$item_card_Data = get_cols_where_row(new Inv_itemCard(), array("does_has_retailunit", "retail_uom_id", "uom_id"), array("item_code" => $item_data_detials['item_code'], "com_code" => $com_code));
if (!empty($item_card_Data)) {
if ($item_card_Data['does_has_retailunit'] == 1) {
$item_card_Data['parent_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['uom_id']));
$item_card_Data['retial_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['retail_uom_id']));
} else {
$item_card_Data['parent_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['uom_id']));
}
}
return view("admin.suppliers_with_orders.load_edit_item_details", ['parent_pill_data' => $parent_pill_data, 'item_data_detials' => $item_data_detials, 'item_cards' => $item_cards, 'item_card_Data' => $item_card_Data]);
}
}
}
}
public function load_modal_add_details(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
$parent_pill_data = get_cols_where_row(new Suppliers_with_orders(), array("is_approved"), array("auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
if (!empty($parent_pill_data)) {
if ($parent_pill_data['is_approved'] == 0) {
$item_cards = get_cols_where(new Inv_itemCard(), array("name", "item_code", "item_type"), array('active' => 1, 'com_code' => $com_code), 'id', 'DESC');
return view("admin.suppliers_with_orders.load_add_new_itemdetails", ['parent_pill_data' => $parent_pill_data, 'item_cards' => $item_cards]);
}
}
}
}
public function edit_item_details(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
$parent_pill_data = get_cols_where_row(new Suppliers_with_orders(), array("is_approved", "order_date", "tax_value", "discount_value"), array("auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
if (!empty($parent_pill_data)) {
if ($parent_pill_data['is_approved'] == 0) {
$data_to_update['item_code'] = $request->item_code_add;
$data_to_update['deliverd_quantity'] = $request->quantity_add;
$data_to_update['unit_price'] = $request->price_add;
$data_to_update['uom_id'] = $request->uom_id_Add;
$data_to_update['isparentuom'] = $request->isparentuom;
if ($request->type == 2) {
$data_to_update['production_date'] = $request->production_date;
$data_to_update['expire_date'] = $request->expire_date;
}
$data_to_update['item_card_type'] = $request->type;
$data_to_update['total_price'] = $request->total_add;
$data_to_update['order_date'] = $parent_pill_data['order_date'];
$data_to_update['updated_by'] = auth()->user()->id;
$data_to_update['updated_at'] = date("Y-m-d H:i:s");
$data_to_update['com_code'] = $com_code;
$flag = update(new Suppliers_with_orders_details(), $data_to_update, array("id" => $request->id, 'com_code' => $com_code, 'order_type' => 1, 'suppliers_with_orders_auto_serial' => $request->autoserailparent));
if ($flag) {
/** update parent pill */
$total_detials_sum = get_sum_where(new Suppliers_with_orders_details(), 'total_price', array("suppliers_with_orders_auto_serial" => $request->autoserailparent, 'order_type' => 1, 'com_code' => $com_code));
$dataUpdateParent['total_cost_items'] = $total_detials_sum;
$dataUpdateParent['total_befor_discount'] = $total_detials_sum + $parent_pill_data['tax_value'];
$dataUpdateParent['total_cost'] = $dataUpdateParent['total_befor_discount'] - $parent_pill_data['discount_value'];
$dataUpdateParent['updated_by'] = auth()->user()->id;
$dataUpdateParent['updated_at'] = date("Y-m-d H:i:s");
update(new Suppliers_with_orders(), $dataUpdateParent, array("auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
echo json_encode("done");
}
}
}
}
}

public function delete($id)
{
try {
$com_code = auth()->user()->com_code;
$parent_pill_data = get_cols_where_row(new Suppliers_with_orders(), array("is_approved", "auto_serial"), array("id" => $id, "com_code" => $com_code, 'order_type' => 1));
if (empty($parent_pill_data)) {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما']);
}
if ($parent_pill_data['is_approved'] == 1) {
if (empty($parent_pill_data)) {
return redirect()->back()
->with(['error' => 'عفوا  لايمكن الحذف بتفاصيل فاتورة معتمده ومؤرشفة']);
}
}
$flag = delete(new Suppliers_with_orders(), array("id" => $id, "com_code" => $com_code, 'order_type' => 1));
if ($flag) {
    //حيتم الحذف بشكل الي من خلال العلاقه بين الجدولين ونقدر نستغني عن الكود الخاص بالحذف 
delete(new Suppliers_with_orders_details(), array("suppliers_with_orders_auto_serial" => $parent_pill_data['auto_serial'], "com_code" => $com_code, 'order_type' => 1));
return redirect()->route('admin.suppliers_orders.index')->with(['success' => 'لقد تم حذف  البيانات بنجاح']);
}
} catch (\Exception $ex) {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()]);
}
}
public function delete_details($id, $parent_id)
{
try {
$com_code = auth()->user()->com_code;
$parent_pill_data = get_cols_where_row(new Suppliers_with_orders(), array("is_approved", "auto_serial"), array("id" => $parent_id, "com_code" => $com_code, 'order_type' => 1));
if (empty($parent_pill_data)) {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما']);
}
if ($parent_pill_data['is_approved'] == 1) {
if (empty($parent_pill_data)) {
return redirect()->back()
->with(['error' => 'عفوا  لايمكن الحذف بتفاصيل فاتورة معتمده ومؤرشفة']);
}
}
$item_row = Suppliers_with_orders_details::find($id);
if (!empty($item_row)) {
$flag = $item_row->delete();
if ($flag) {
/** update parent pill */
$total_detials_sum = get_sum_where(new Suppliers_with_orders_details(), 'total_price', array("suppliers_with_orders_auto_serial" => $parent_pill_data['auto_serial'], 'order_type' => 1, 'com_code' => $com_code));
$dataUpdateParent['total_cost_items'] = $total_detials_sum;
$dataUpdateParent['total_befor_discount'] = $total_detials_sum + $parent_pill_data['tax_value'];
$dataUpdateParent['total_cost'] = $dataUpdateParent['total_befor_discount'] - $parent_pill_data['discount_value'];
$dataUpdateParent['updated_by'] = auth()->user()->id;
$dataUpdateParent['updated_at'] = date("Y-m-d H:i:s");
update(new Suppliers_with_orders(), $dataUpdateParent, array("id" => $parent_id, "com_code" => $com_code, 'order_type' => 1));
return redirect()->back()
->with(['success' => '   تم حذف البيانات بنجاح']);
} else {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما']);
}
} else {
return redirect()->back()
->with(['error' => 'عفوا غير قادر الي الوصول للبيانات المطلوبة']);
}
} catch (\Exception $ex) {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()]);
}
}


public function load_modal_approve_invoice(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
$data = get_cols_where_row(new Suppliers_with_orders(), array("*"), array("auto_serial" => $request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
//current user shift
$user_shift = get_user_shift(new Admins_Shifts(), new Treasuries(), new Treasuries_transactions());
$counterDetails=get_count_where(new Suppliers_with_orders_details(),array("suppliers_with_orders_auto_serial"=>$request->autoserailparent, "com_code" => $com_code, 'order_type' => 1));
return view("admin.suppliers_with_orders.load_modal_approve_invoice", ['data' => $data, 'user_shift' => $user_shift,'counterDetails'=>$counterDetails]);
}
}
public function load_usershiftDiv(Request $request)
{
if ($request->ajax()) {
$com_code = auth()->user()->com_code;
//current user shift
$user_shift = get_user_shift(new Admins_Shifts(), new Treasuries(), new Treasuries_transactions());
}
return view("admin.suppliers_with_orders.load_usershiftDiv", ['user_shift' => $user_shift]);
}

//اعتماد وترحيل فاتورة المشتريات 
function do_approve($auto_serial, Request $request)
{
$com_code = auth()->user()->com_code;
//check is not approved 
$data = get_cols_where_row(new Suppliers_with_orders(), array("total_cost_items", "is_approved", "id", "account_number", "store_id", "suuplier_code"), array("auto_serial" => $auto_serial, "com_code" => $com_code, 'order_type' => 1));
if (empty($data)) {
return redirect()->route("admin.suppliers_orders.index")->with(['error' => "عفوا غير قادر علي الوصول الي البيانات المطلوبة !!"]);
}
$SupplierName = get_field_value(new Supplier(), "name", array("com_code" => $com_code, "suuplier_code" => $data['suuplier_code']));
if ($data['is_approved'] == 1) {
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['error' => "عفوا لايمكن اعتماد فاتورة معتمده من قبل !!"]);
}
$counterDetails=get_count_where(new Suppliers_with_orders_details(),array("suppliers_with_orders_auto_serial"=>$auto_serial, "com_code" => $com_code, 'order_type' => 1));
if ($counterDetails== 0) {
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['error' => "عفوا لايمكن اعتماد الفاتورة قبل اضافة الأصناف عليها !!!            "]);
}
$dataUpdateParent['tax_percent'] = $request['tax_percent'];
$dataUpdateParent['tax_value'] = $request['tax_value'];
$dataUpdateParent['total_befor_discount'] = $request['total_befor_discount'];
$dataUpdateParent['discount_type'] = $request['discount_type'];
$dataUpdateParent['discount_percent'] = $request['discount_percent'];
$dataUpdateParent['discount_value'] = $request['discount_value'];
$dataUpdateParent['total_cost'] = $request['total_cost'];
$dataUpdateParent['pill_type'] = $request['pill_type'];
$dataUpdateParent['money_for_account'] = $request['total_cost'] * (-1);
$dataUpdateParent['is_approved'] = 1;
$dataUpdateParent['approved_by'] = auth()->user()->com_code;
$dataUpdateParent['updated_at'] = date("Y-m-d H:i:s");
$dataUpdateParent['updated_by'] = auth()->user()->com_code;
//first check for pill type sate cash
if ($request['pill_type'] == 1) {
if ($request['what_paid'] != $request['total_cost']) {
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['error' => "عفوا يجب ان يكون المبلغ بالكامل مدفوع في حالة الفاتورة كاش !!"]);
}
}
//second  check for pill type sate agel
if ($request['pill_type'] == 2) {
if ($request['what_paid'] == $request['total_cost']) {
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['error' => "عفوا يجب ان لايكون المبلغ بالكامل مدفوع في حالة الفاتورة اجل !!"]);
}
}
$dataUpdateParent['what_paid'] = $request['what_paid'];
$dataUpdateParent['what_remain'] = $request['what_remain'];
//thaird  check for what paid 
if ($request['what_paid'] > 0) {
if ($request['what_paid'] > $request['total_cost']) {
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['error' => "عفوا يجب ان لايكون المبلغ المدفوع اكبر من اجمالي الفاتورة      !!"]);
}
//check for user shift
$user_shift = get_user_shift(new Admins_Shifts(), new Treasuries(), new Treasuries_transactions());
//chehck if is empty
if (empty($user_shift)) {
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['error' => " عفوا لاتملتك الان شفت خزنة مفتوح لكي تتمكن من اتمام عمليه الصرف"]);
}
//check for blance
if ($user_shift['balance'] < $request['what_paid']) {
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['error' => " عفوا لاتملتك الان رصيد كافي بخزنة الصرف  لكي تتمكن من اتمام عمليه الصرف"]);
}
}
$flag = update(new Suppliers_with_orders(), $dataUpdateParent, array("auto_serial" => $auto_serial, "com_code" => $com_code, 'order_type' => 1));
if ($flag) {
//Affect on Supplier Balance  حنأثر في رصيد المورد
//حنجيب  سجل المورد من الشجره المحاسبية برقم الحساب المالب
//حركات  مختلفه
//first make treasuries_transactions  action if what paid >0
if ($request['what_paid'] > 0) {
//first get isal number with treasuries 
$treasury_date = get_cols_where_row(new Treasuries(), array("last_isal_exhcange"), array("com_code" => $com_code, "id" => $user_shift['treasuries_id']));
if (empty($treasury_date)) {
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['error' => " عفوا غير قادر علي الوصول الي بيانات الخزنة المطلوبة"]);
}
$last_record_treasuries_transactions_record = get_cols_where_row_orderby(new Treasuries_transactions(), array("auto_serial"), array("com_code" => $com_code), "auto_serial", "DESC");
if (!empty($last_record_treasuries_transactions_record)) {
$dataInsert_treasuries_transactions['auto_serial'] = $last_record_treasuries_transactions_record['auto_serial'] + 1;
} else {
$dataInsert_treasuries_transactions['auto_serial'] = 1;
}
$dataInsert_treasuries_transactions['isal_number'] = $treasury_date['last_isal_exhcange'] + 1;
$dataInsert_treasuries_transactions['shift_code'] = $user_shift['shift_code'];
//Credit دائن
$dataInsert_treasuries_transactions['money'] = $request['what_paid'] * (-1);
$dataInsert_treasuries_transactions['treasuries_id'] = $user_shift['treasuries_id'];
$dataInsert_treasuries_transactions['mov_type'] = 9;
$dataInsert_treasuries_transactions['move_date'] = date("Y-m-d");
$dataInsert_treasuries_transactions['account_number'] = $data["account_number"];
$dataInsert_treasuries_transactions['is_account'] = 1;
$dataInsert_treasuries_transactions['is_approved'] = 1;
$dataInsert_treasuries_transactions['the_foregin_key'] = $data["auto_serial"];
//debit مدين
$dataInsert_treasuries_transactions['money_for_account'] = $request['what_paid'];
$dataInsert_treasuries_transactions['byan'] = "صرف نظير فاتورة مشتريات  رقم" . $auto_serial;
$dataInsert_treasuries_transactions['created_at'] = date("Y-m-Y H:i:s");
$dataInsert_treasuries_transactions['added_by'] = auth()->user()->id;
$dataInsert_treasuries_transactions['com_code'] = $com_code;
$flag = insert(new Treasuries_transactions(), $dataInsert_treasuries_transactions);
if ($flag) {
//update Treasuries last_isal_collect
$dataUpdateTreasuries['last_isal_exhcange'] = $dataInsert_treasuries_transactions['isal_number'];
update(new Treasuries(), $dataUpdateTreasuries, array("com_code" => $com_code, "id" => $user_shift['treasuries_id']));
}
}
refresh_account_blance_supplier($data['account_number'], new Account(), new Supplier(), new Treasuries_transactions(), new Suppliers_with_orders(),new services_with_orders(), false);
//store move حركة المخزن
//first Get item card data جنجيب الاصناف اللي علي الفاتورة
$items = get_cols_where(new Suppliers_with_orders_details(), array("*"), array("suppliers_with_orders_auto_serial" => $auto_serial, "com_code" => $com_code, "order_type" => 1), "id", "ASC");
if (!empty($items)) {
foreach ($items as $info) {
//get itemCard Data
$itemCard_Data = get_cols_where_row(new Inv_itemCard(), array("uom_id", "retail_uom_quntToParent", "retail_uom_id", "does_has_retailunit"), array("com_code" => $com_code, "item_code" => $info->item_code));
if (!empty($itemCard_Data)) {
//get Quantity Befor any Action  حنجيب كيمة الصنف بكل المخازن قبل الحركة
$quantityBeforMove = get_sum_where(new Inv_itemcard_batches(), "quantity", array("item_code" => $info->item_code, "com_code" => $com_code));
//get Quantity Befor any Action  حنجيب كيمة الصنف  بمخزن فاتورة المشتريات الحالي قبل الحركة
$quantityBeforMoveCurrntStore = get_sum_where(new Inv_itemcard_batches(), "quantity", array("item_code" => $info->item_code, "com_code" => $com_code, 'store_id' => $data['store_id']));
$MainUomName = get_field_value(new Inv_uom(), "name", array("com_code" => $com_code, "id" => $itemCard_Data['uom_id']));
//if is parent Uom لو وحده اب
if ($info->isparentuom == 1) {
$quntity = $info->deliverd_quantity;
$unit_price = $info->unit_price;
} else {
// if is retail  لو كان بوحده الابن التجزئة
//التحويل من الاب للابن بنضرب   في النسبة بينهم - اما التحويل من الابن للاب بنقسم علي النسبه بينهما 
$quntity = ($info->deliverd_quantity / $itemCard_Data['retail_uom_quntToParent']);
$unit_price = $info->unit_price * $itemCard_Data['retail_uom_quntToParent'];
}
//بندخل الكميات للمخزن بوحده القياس الاب  اجباري 
//لو الصنف استهلاكي له تاريخ صلاحيه وانتاج فبعمل تحقق بسعر الشراء مع التواريخ
//لو الصنف  غير استهلاكي يبقي بعمل تحقق فقط بسعر الشراء
if ($info->item_card_type == 2) {
//استهلاكي بتواريخ 
$dataInsertBatch["store_id"] = $data['store_id'];
$dataInsertBatch["item_code"] = $info->item_code;
$dataInsertBatch["production_date"] = $info->production_date;
$dataInsertBatch["expired_date"] = $info->expire_date;
$dataInsertBatch["unit_cost_price"] = $unit_price;
$dataInsertBatch["inv_uoms_id"] = $itemCard_Data['uom_id'];
} else {
//بسعر فقط
$dataInsertBatch["store_id"] = $data['store_id'];
$dataInsertBatch["item_code"] = $info->item_code;
$dataInsertBatch["unit_cost_price"] = $unit_price;
$dataInsertBatch["inv_uoms_id"] = $itemCard_Data['uom_id'];
}
$OldBatchExsists = get_cols_where_row(new Inv_itemcard_batches(), array("quantity", "id", "unit_cost_price"), $dataInsertBatch);
if (!empty($OldBatchExsists)) {
//update current Batch تحديث علي الباتش القديمة
$dataUpdateOldBatch['quantity'] = $OldBatchExsists['quantity'] + $quntity;
$dataUpdateOldBatch['total_cost_price'] = $OldBatchExsists['unit_cost_price'] * $dataUpdateOldBatch['quantity'];
$dataUpdateOldBatch["updated_at"] = date("Y-m-d H:i:s");
$dataUpdateOldBatch["updated_by"] = auth()->user()->id;
update(new Inv_itemcard_batches(), $dataUpdateOldBatch, array("id" => $OldBatchExsists['id'], "com_code" => $com_code));
} else {
//insert new Batch ادخال باتش جديده
$dataInsertBatch["quantity"] = $quntity;
$dataInsertBatch["total_cost_price"] = $info->total_price;
$dataInsertBatch["created_at"] = date("Y-m-d H:i:s");
$dataInsertBatch["added_by"] = auth()->user()->id;
$dataInsertBatch["com_code"] = $com_code;
$row = get_cols_where_row_orderby(new Inv_itemcard_batches(), array("auto_serial"), array("com_code" => $com_code), 'id', 'DESC');
if (!empty($row)) {
$dataInsertBatch['auto_serial'] = $row['auto_serial'] + 1;
} else {
$dataInsertBatch['auto_serial'] = 1;
}
insert(new Inv_itemcard_batches(), $dataInsertBatch);
}
//كمية الصنف بكل المخازن بعد اتمام حركة الباتشات وترحيلها
$quantityAfterMove = get_sum_where(new Inv_itemcard_batches(), "quantity", array("item_code" => $info->item_code, "com_code" => $com_code));
//كمية الصنف بمخزن فاتورة الشراء  بعد اتمام حركة الباتشات وترحيلها
$quantityAfterMoveCurrentStore = get_sum_where(new Inv_itemcard_batches(), "quantity", array("item_code" => $info->item_code, "com_code" => $com_code, 'store_id' => $data['store_id']));
$dataInsert_inv_itemcard_movements['inv_itemcard_movements_categories'] = 1;
$dataInsert_inv_itemcard_movements['items_movements_types'] = 1;
$dataInsert_inv_itemcard_movements['item_code'] = $info->item_code;
//كود الفاتورة الاب
$dataInsert_inv_itemcard_movements['FK_table'] = $auto_serial;
//كود صف الابن بتفاصيل الفاتورة
$dataInsert_inv_itemcard_movements['FK_table_details'] = $info->id;
$dataInsert_inv_itemcard_movements['byan'] = "نظير مشتريات من المورد " . " " . $SupplierName . " فاتورة رقم" . " " . $auto_serial;
//كمية الصنف بكل المخازن قبل الحركة
$dataInsert_inv_itemcard_movements['quantity_befor_movement'] = "عدد " . " " . ($quantityBeforMove * 1) . " " . $MainUomName;
// كمية الصنف بكل المخازن بعد  الحركة
$dataInsert_inv_itemcard_movements['quantity_after_move'] = "عدد " . " " . ($quantityAfterMove * 1) . " " . $MainUomName;
//كمية الصنف  المخزن الحالي قبل الحركة
$dataInsert_inv_itemcard_movements['quantity_befor_move_store'] = "عدد " . " " . ($quantityBeforMoveCurrntStore * 1) . " " . $MainUomName;
// كمية الصنف بالمخزن الحالي بعد الحركة الحركة
$dataInsert_inv_itemcard_movements['quantity_after_move_store'] = "عدد " . " " . ($quantityAfterMoveCurrentStore * 1) . " " . $MainUomName;
$dataInsert_inv_itemcard_movements["store_id"] = $data['store_id'];
$dataInsert_inv_itemcard_movements["created_at"] = date("Y-m-d H:i:s");
$dataInsert_inv_itemcard_movements["added_by"] = auth()->user()->id;
$dataInsert_inv_itemcard_movements["date"] = date("Y-m-d");
$dataInsert_inv_itemcard_movements["com_code"] = $com_code;
insert(new Inv_itemcard_movements(), $dataInsert_inv_itemcard_movements);
//item Move Card حركة الصنف 
}
//update last Cost price   تحديث اخر سعر شراء للصنف
if ($info->isparentuom == 1) {
//لو الوحده اللي اشتريت بيها كانت وحده اب 
$dataUpdateItemCardCosts['cost_price'] = $info->unit_price;
if ($itemCard_Data['does_has_retailunit'] == 1) {
$dataUpdateItemCardCosts['cost_price_retail'] = $info->unit_price / $itemCard_Data['retail_uom_quntToParent'];
}
} else {
// if is retail  لو كان بوحده الابن التجزئة
//التحويل من الاب للابن بنضرب   في النسبة بينهم - اما التحويل من الابن للاب بنقسم علي النسبه بينهما 
$dataUpdateItemCardCosts['cost_price'] = $info->unit_price * $itemCard_Data['retail_uom_quntToParent'];
$dataUpdateItemCardCosts['cost_price_retail'] = $info->unit_price;
}
update(new Inv_itemCard(), $dataUpdateItemCardCosts, array("com_code" => $com_code, "item_code" => $info->item_code));
// update itemcard Quantity mirror  تحديث المرآه الرئيسية للصنف
do_update_itemCardQuantity(new Inv_itemCard(), $info->item_code, new Inv_itemcard_batches(), $itemCard_Data['does_has_retailunit'], $itemCard_Data['retail_uom_quntToParent']);
}
}
return redirect()->route("admin.suppliers_orders.show", $data['id'])->with(['success' => " تم اعتماد وترحيل الفاتورة بنجاح  "]);
}
}



public function ajax_search(Request $request)
{
if ($request->ajax()) {
$search_by_text = $request->search_by_text;
$suuplier_code = $request->suuplier_code;
$store_id = $request->store_id;
$order_date_form = $request->order_date_form;
$order_date_to = $request->order_date_to;
$searchbyradio = $request->searchbyradio;
if ($suuplier_code == 'all') {
//دائما  true
$field1 = "id";
$operator1 = ">";
$value1 = 0;
} else {
$field1 = "suuplier_code";
$operator1 = "=";
$value1 = $suuplier_code;
}
if ($store_id == 'all') {
//دائما  true
$field2 = "id";
$operator2 = ">";
$value2 = 0;
} else {
$field2 = "store_id";
$operator2 = "=";
$value2 = $store_id;
}
if ($order_date_form == '') {
//دائما  true
$field3 = "id";
$operator3 = ">";
$value3 = 0;
} else {
$field3 = "order_date";
$operator3 = ">=";
$value3 = $order_date_form;
}
if ($order_date_to == '') {
//دائما  true
$field4 = "id";
$operator4 = ">";
$value4 = 0;
} else {
$field4 = "order_date";
$operator4 = "<=";
$value4 = $order_date_to;
}
if ($search_by_text != '') {
if ($searchbyradio == 'auto_serial') {
$field5 = "auto_serial";
$operator5 = "=";
$value5 = $search_by_text;
} else {
$field5 = "auto_serial";
$operator5 = "=";
$value5 = $search_by_text;
}
} else {
//true 
$field5 = "id";
$operator5 = ">";
$value5 = 0;
}
$data = Suppliers_with_orders::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->where($field3, $operator3, $value3)->where($field4, $operator4, $value4)->where($field5, $operator5, $value5)->where('order_type','=',1)->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
if (!empty($data)) {
foreach ($data as $info) {
$info->added_by_admin = Admin::where('id', $info->added_by)->value('name');
$info->supplier_name = Supplier::where('suuplier_code', $info->suuplier_code)->value('name');
$info->store_name = Store::where('id', $info->store_id)->value('name');
if ($info->updated_by > 0 and $info->updated_by != null) {
$info->updated_by_admin = Admin::where('id', $info->updated_by)->value('name');
}
}
}
return view('admin.suppliers_with_orders.ajax_search', ['data' => $data]);
}
}

public function printsaleswina4($id,$size){

try {
$com_code = auth()->user()->com_code;
$invoice_data = get_cols_where_row(new Suppliers_with_orders(), array("*"), array("id" => $id, "com_code" => $com_code, 'order_type' => 1));
if (empty($invoice_data)) {
return redirect()->route('admin.suppliers_orders.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
}
$invoice_data['added_by_admin'] = Admin::where('id', $invoice_data['added_by'])->value('name');
$invoice_data['supplier_name'] = Supplier::where('suuplier_code', $invoice_data['suuplier_code'])->value('name');
$invoice_data['supplier_phone'] = Supplier::where('suuplier_code', $invoice_data['suuplier_code'])->value('phones');

$invoice_data['store_name'] = Store::where('id', $invoice_data['store_id'])->value('name');

$invoices_details = get_cols_where(new Suppliers_with_orders_details(), array("*"), array('suppliers_with_orders_auto_serial' => $invoice_data['auto_serial'], 'order_type' => 1, 'com_code' => $com_code), 'id', 'DESC');
if (!empty($invoices_details)) {
foreach ($invoices_details as $info) {
$info->item_card_name = Inv_itemCard::where('item_code', $info->item_code)->value('name');
$info->uom_name = get_field_value(new Inv_uom(), "name", array("id" => $info->uom_id));
}
}
$systemData=get_cols_where_row(new Admin_panel_setting(),array("system_name","phone","address","photo"),array("com_code"=>$com_code));

if($size=="A4"){
    return view('admin.suppliers_with_orders.printsaleswina4',['data'=>$invoice_data,'systemData'=>$systemData,'sales_invoices_details'=>$invoices_details]);
}else{
    return view('admin.suppliers_with_orders.printsaleswina6',['data'=>$invoice_data,'systemData'=>$systemData,'sales_invoices_details'=>$invoices_details]);

}
} catch (\Exception $ex) {
return redirect()->back()
->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()]);
}
}

}