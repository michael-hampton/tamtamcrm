<link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
<link href="{{ asset('js/bootstrap.js') }}" rel="stylesheet">

<style>
    /*
*
* ==========================================
* FOR DEMO PURPOSES
* ==========================================
*
*/

    body {
        background: #f5f5f5;
    }

    .rounded-lg {
        border-radius: 1rem;
    }

    .nav-pills .nav-link {
        color: #555;
    }

    .nav-pills .nav-link.active {
        color: #fff;
    }

</style>


<div class="container py-5">

    <!-- For demo purpose -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4">Bootstrap 4 credit card form</h1>
            <p class="lead mb-0">Easily build a well-structured credit card form using Bootstrap 4</p>
            <p class="lead">Snippet by <a href="https://bootstrapious.com/snippets">Bootstrapious</a></p>
        </div>
    </div>
    <!-- End -->


    <form style="float:right" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" >

        <input type="hidden" name="cmd" value="_cart">
        <input type="hidden" name="return" value="{{ $return_url }}">
        <input type="hidden" name="upload" value="1">
        <input type="hidden" name="business" value="michaelhamptondesign@yahoo.com">

        @foreach($invoice->line_items as $key => $line_item)

            <?php if ($line_item->type_id !== \App\Models\Invoice::PRODUCT_TYPE) {
                continue;
            }

            $product = \App\Models\Product::where('id', '=', $line_item->product_id)->first();

            ?>
            <input type="hidden" name="item_name_{{ $key === 0 ? 1 : $key }}" value="{{ $product->name }}">
            <input type="hidden" name="amount_{{ $key === 0 ? 1 : $key }}" value="{{ $line_item->unit_price }}">
            <input type="hidden" name="quantity_{{ $key === 0 ? 1 : $key }}" value="{{ $line_item->quantity }}">

        @endforeach

{{--        <input type="hidden" name="item_name_1" value="Paper">--}}
{{--        <input type="hidden" name="amount_1" value="20">--}}
{{--        <input type="hidden" name="shipping_1" value="3.99">--}}
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="button_subtype" value="services">
        <input type="hidden" name="no_note" value="0">
        <input type="hidden" name="cn" value="Add special instructions to the seller:">

        <input type="hidden" name="no_shipping" value="2">

        <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>

</div>

<script>
    document.querySelector('form').submit();
</script>
