@extends('front/eshopper/layouts/index')
@section('slider')
@overwrite
@section('sidebar')
@overwrite
@section('css')
<style>
    .autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
    .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
    .autocomplete-selected { background: #F0F0F0; }
    .autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
    .autocomplete-group { padding: 2px 5px; }
    .autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
</style>
@endsection
@section('js')
<script src="{{asset('front/eshopper/js/jquery.autocomplete.min.js')}}"></script>
<script>
$(document).ready(function() {
    var countries = [];
    $.get('{{url("api/ongkir/city")}}', function(e) {
        $('#city_asal').autocomplete({
            lookup: e,
            onSelect: function(suggestion) {
                $('#city_asal_hid').val(suggestion.data);
            }
        });
        $('#city_tujuan').autocomplete({
            lookup: e,
            onSelect: function(suggestion) {
                $('#city_tujuan_hid').val(suggestion.data);
            }
        });
    });
    $('#cek-ongkir').click(function(e) {
        e.preventDefault();
        var telo = {'_token': "{{csrf_token()}}", 'data': $('#jne_ongkir').serialize()};
        $.post('{{url("api/ongkir/tariff")}}', telo, function(result) {
            $('.user_option').html(result);
        }, "html").done(function() {
            var province = $('#jne_ongkir').find('#province').val();
            var city = $('#jne_ongkir').find('#city_name').val();
            simpleCart({
                checkout: {
                    type: "SendForm",
                    method: "POST",
                    url: "{{url('checkout/login')}}",
                    extra_data: {
                        _token: "{{csrf_token()}}",
                        city: city,
                        province: province,
                    }
                }
            });
        });
    });
    simpleCart.bind('beforeCheckout', function(data) {
        var tarif = $(document).find('.tarif');
        if ($("#city_tujuan").val() == '') {
            alert('Kota Tujuan harap diisi');
            return false;
        }
        if (tarif.length === 0) {
            alert('Tekan Tombol Cek Ongkir terlebih dahulu');
            return false;
        }
        if (!$("input[name='tarif']:checked").val()) {
            alert('Pilih Ongkos Kirim!');
            return false;
        }
    });
    $(document).on('change', '.tarif', function() {
        kampret = $(this).val();
        var telo = simpleCart.shipping(function() {
            return parseInt(kampret);
        });
        simpleCart.update();
    });

});
</script>
@endsection
@section('main')
<section id="cart_items">
    <div class="container">
        <div class="breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li class="active">Shopping Cart</li>
            </ol>
        </div>
        <div class="table-responsive cart_info">
            <div class="anotherCart_items"></div>
        </div>
    </div>
</section> <!--/#cart_items-->

<section id="do_action">
    <div class="container">
        <div class="heading">
            <h3>Ongkos Kirim</h3>
            <p>Choose if you have a discount code or reward points you want to use or would like to estimate your delivery cost.</p>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="chose_area">
                    <!--                    <ul class="user_option">
                                            <li>
                                                <input type="checkbox">
                                                <label>Use Coupon Code</label>
                                            </li>
                                            <li>
                                                <input type="checkbox">
                                                <label>Use Gift Voucher</label>
                                            </li>
                                            <li>
                                                <input type="checkbox">
                                                <label>Estimate Shipping & Taxes</label>
                                            </li>
                                        </ul>-->
                    <form id='jne_ongkir'>
                        <ul class="user_info">
                            <li class="single_field zip-field">
                                <label>Kota asal:</label>
                                <input type="text" id="city_asal" value="Yogyakarta" disabled/>
                                <input type="hidden" name="origin" id="city_asal_hid" value="501"/>
                            </li>
                            <li class="single_field zip-field">
                                <label>Kota Tujuan:</label>
                                <input type="text" id="city_tujuan"/>
                                <input type="hidden" name="destination" id="city_tujuan_hid" value=''/>
                            </li>
                            <input type="hidden" name="weight" id="city_tujuan_hid" value='1000'/>
                            <li class="single_field zip-field">
                                <label></label>
                                <button type='submit' class="btn btn-default update" id='cek-ongkir'>Cek Ongkir</button>
                            </li>
                        </ul>
                        <div class="row" style="width: 500px; height: 50px; margin-left: 100px;">
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="courier" id="optionsRadios1" value="jne" checked>
                                    JNE
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="courier" id="optionsRadios2" value="pos">
                                    POS
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="courier" id="optionsRadios3" value="tiki">
                                    TIKI
                                </label>
                            </div>
                        </div>
                        <ul class='user_option'>

                        </ul>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="total_area">
                    <ul>
                        <li>Cart Sub Total <span class="simpleCart_total"></span></li>
<!--                        <li>Eco Tax <span>$2</span></li>-->
                        <li>Shipping Cost <span class="simpleCart_shipping"></span></li>
                        <li>Total <span class="simpleCart_grandTotal"></span></li>
                    </ul>
                    <a href="javascript:;" class="simpleCart_checkout btn btn-default check_out">Checkout</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section><!--/#do_action-->
@stop