<?php
require_once 'config/stripe.php';
// session_start();
// if(isset($_SESSION["shoppingCart"])){
//     $shoppingcart =json_encode($_SESSION["shoppingCart"]);
// }else{
//     $shoppingcart = json_encode('empty');
// }

?>
<html>
<HEAD><link rel="icon" href="favicon.ico" type="image/x-icon" /> <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <!-- Google tag (gtag.js) --> <script async src="https://www.googletagmanager.com/gtag/js?id=AW-1030793338"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-1030793338'); </script>
<title>Cart page</title>
<meta name="viewport" content="width=device-width,initial-scale=.34" user-scalable="yes">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/styles.css">
<script
src="https://code.jquery.com/jquery-3.6.3.min.js"
integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
crossorigin="anonymous"></script>
<script src="./js/cart.js"></script>
<script src="./js/app.js"></script>
<script src="./js/expedited_rates.js"></script>
<script src="https://js.stripe.com/v3/"></script>
  <script>
 

      var orderTotal = 0
      var subTotal = 0
      var handlingTotal=0;
      var shippingTotal = 0;
      let hasExpeditedShipping = false;
        
    //   const localPickup = localStorage.getItem('localPickup')==true?true:false;
      
     
    
      $(document).ready(()=>{
        // Pickup selection should never carry over between visits — always start unchecked.
        localStorage.setItem('localPickup', false);
        localStorage.setItem('pickupType', '');
        $('#local-pickup, #priority-pickup').prop('checked', false);

         function calculateShipping(amount){
        if(localStorage.getItem('localPickup')=='true'){
           return 0;
        }
            if(amount>0 && amount<=30){
                return 14.99
            }else if(amount>30 && amount <= 80){
                return 15.99
            }else if (amount>80 && amount<=140){
                return 19.99
            }else if(amount>140 && amount <=250){
                return 22.99
            }else{return 0}
     
      }
// Recomputes handling/shipping/tax/total in place after a pickup toggle, without
// reloading the page (which would wipe out any Stripe fields already filled in).
function applyPickupPricing(){
    let cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    let isPickup = localStorage.getItem('localPickup') == 'true';
    handlingTotal = 0;

    cart.forEach(function(cartItem, index){
        let cartItemHandling = 0;
        if(!isPickup){
            if(handlingTotal==0){
                if(cartItem.item_quantity>1){
                    cartItemHandling = +cartItem.item_handling + (cartItem.item_quantity-1)*cartItem.item_handling/2;
                }else{
                    cartItemHandling+= +cartItem.item_handling;
                }
            }else{
                cartItemHandling+= +(cartItem.item_handling/2)*(cartItem.item_quantity);
            }
        }else{
            if(localStorage.getItem('pickupType')=='normal'){
                cartItemHandling = 0;
            }else{
                cartItemHandling = index==0 ? 10 : 0;
            }
        }
        handlingTotal += cartItemHandling;

        $('#paypal-form [name="handling_'+(index+1)+'"]').val(cartItemHandling.toFixed(2));
        if(cartItem.item_number && cartItem.item_number.length>0){
            let $shipping2 = $('#paypal-form [name="shipping2_'+(index+1)+'"]');
            let $shipping  = $('#paypal-form [name="shipping_'+(index+1)+'"]');
            if(isPickup){
                $shipping2.val('0.00');
                if($shipping.length){ $shipping.val('0.00'); }
                else { $('#paypal-form').append('<input type="hidden" name="shipping_'+(index+1)+'" value="0.00">'); }
            }else{
                $shipping2.val(cartItem.item_handling);
                $shipping.remove();
            }
        }
    });

    $('#paypal-form input[name="custom"]').remove();
    if(isPickup){
        let customVal = localStorage.getItem('pickupType')=='normal' ? 'LOCAL PICKUP SELECTED' : 'PRIORITY PICKUP SELECTED';
        $('#paypal-form').append('<input type="hidden" name="custom" value="'+customVal+'">');
    }

    shippingTotal = calculateShipping(subTotal);
    handlingTotal += shippingTotal;
    orderTotal = subTotal + handlingTotal;

    $('#s-h').text('$'+handlingTotal.toFixed(2));
    $('input[name="handling_cart"]').val(handlingTotal.toFixed(2));

    // Drop any stale tariff line from a prior recompute — the tax-country handler
    // below rebuilds it fresh and doesn't dedupe on repeat calls.
    $('#paypal-form input[value="Tariff Fees"]').each(function(){
        let n = $(this).attr('name').split('_').pop();
        $('#paypal-form input[name="amount_'+n+'"]').remove();
        $('#paypal-form input[name="quantity_'+n+'"]').remove();
        $('#paypal-form input[name="handling_'+n+'"]').remove();
        $(this).remove();
    });

    // Total (and the Stripe PaymentIntent amount) only exist once a country is
    // picked — refresh via the existing tax handlers so nothing else resets.
    let country = $('#tax-country').val();
    if(country){
        let province = $('#tax-province').val();
        if(country=='CA' && province){
            $('#tax-province').trigger('change');
        }else{
            $('#tax-country').trigger('change');
        }
    }
}

$('.local').on('click',function(){
    if($(this).is(':checked')){
        localStorage.setItem('localPickup',true);
        localStorage.setItem('pickupType',$(this).val());
        if($(this).val()=='normal'){
            $('#priority-pickup').prop('checked',false);
        }else{
            $('#local-pickup').prop('checked',false);
        }
    }else{
        localStorage.setItem('localPickup',false);
        localStorage.setItem('pickupType','');
    }
    applyPickupPricing();
});
    //   $('#local-pickup').on('change',function(){
    //         localStorage.setItem('localPickup',$(this).is(':checked'));
    //         setTimeout(() => {
    //         location.reload();
    //     }, 300); 
    //   });
  
        $('#summary').on("click", ".decrement-quantity", function () {
            let itemID = $(this).data('itemId');
            let itemCount = $(this).data('itemCount');
            if(itemCount>1){
                let itemID = $(this).data('itemId');
           
            let cart = JSON.parse(localStorage.getItem('shopping-cart'));
            // console.log(cart);
            cart[itemID].item_quantity -= 1;
            localStorage.setItem("shopping-cart",JSON.stringify(cart));
            location.reload();
            }     
        });
        $('#summary').on("click", ".increment-quantity", function () {
            let itemID = $(this).data('itemId');
           
            let cart = JSON.parse(localStorage.getItem('shopping-cart'));
            // console.log(cart);
            cart[itemID].item_quantity += 1;
            localStorage.setItem("shopping-cart",JSON.stringify(cart));
            location.reload();
       
        });
        $('#summary').on('click','.delete-button-image',e=>{

            const itemToRemove = $(e.target).data('itemId');
            let cart = JSON.parse(localStorage.getItem('shopping-cart'));
            cart.splice(itemToRemove,1);
            // $.ajax({
            //     url:'./session/session.php',
            //     type:'post',
            //     dataType: 'json',
            //     data: {func: "removeItem", itemId: itemToRemove},
                
            // }).done(function(result){
                // console.log(JSON.stringify(result));
                // console.log(result)
            localStorage.setItem('shopping-cart',JSON.stringify(cart));
            location.reload();
        // })
 
      })

        function processCart(result){
            // const theOriginalCart = <?php //echo $shoppingcart;?>;
let cart = JSON.parse(localStorage.getItem("shopping-cart"));
// console.log(cart);
            // const theOriginalCart = JSON.parse(localStorage.getItem("shopping-cart"));
            // let cart;
            let hasRodInCart = false;
         
            // if(result){
            //     cart=result;
            // }else{
            //     cart=theOriginalCart;
            // }
       if(cart=='undefined'||cart.length==0){
            $('#summary').append("<span class='empty-cart'>The cart is empty</span>");
            $('.paypal-button-container').hide();
            $('.totals-section').hide();
            return;
        }
        // console.log(cart);
        if(cart && cart.length>0){
// if(localStorage.getItem('localPickup')=='true'){
//     $('#paypal-form').append('<input type="hidden" name="no_shipping" value="0">');
// }
$('#paypal-form').append('<input type="hidden" name="handling_cart" value="0">');

let items = cart.map((cartItem,index)=>{
    let cartItemHandling=0;
    let itemNumber =cartItem.item_number;
    let prefixes = ['RBB','FR-','RB:','FR:','RC' ];
    const hasRodInCart = prefixes.some(prefix=> itemNumber.toUpperCase().includes(prefix));
    //console.log('item number: '+ itemNumber +', has rod? '+hasRod);
    //if(itemNumber.includes('FR-')){
    //    hasRodInCart = true;
        
        
    //    $("#rodincart").val(1);
    //    $("#rodincartUS").val(1);
    //}
    //console.log("hasrodincart : "+ hasRodInCart);
    if(itemNumber.includes('es-')){
        hasExpeditedShipping = true;
    }
    
    $('#paypal-form').append('<input type="hidden" name="amount_'+(index+1)+'" value="'+cartItem.item_price+'">');
    let itemName =cartItem.item_name;
    itemName += cartItem.item_on0.length>0?", " + cartItem.item_on0 +':'+cartItem.item_os0:'';
    itemName += cartItem.item_on1.length>0?", "+cartItem.item_on1+':'+cartItem.item_os1:'';
    itemName += cartItem.item_on2.length>0?", "+cartItem.item_on2+':'+cartItem.item_os2:'';
    itemName += cartItem.item_on3.length>0?", "+cartItem.item_on3+':'+cartItem.item_os3:'';
    itemName += cartItem.item_on4.length>0?","+cartItem.item_on4+':'+cartItem.item_os4:'';
 
                $('#paypal-form').append('<input type="hidden" name="item_name_'+(index+1)+'" value="'+itemName+'">');
                
                if(cartItem.item_number && cartItem.item_number.length>0){
                    if(localStorage.getItem('localPickup')!='true'){
                    $('#paypal-form').append('<input type="hidden" name="shipping2_'+(index+1)+'" value="'+cartItem.item_handling+'">');
                    // $('#paypal-form').append('<input type="hidden" name="shipping" value="'+cartItem.item_handling+'">');
                  }else{
                    $('#paypal-form').append('<input type="hidden" name="shipping_'+(index+1)+'" value="0.00">');
                    $('#paypal-form').append('<input type="hidden" name="shipping2_'+(index+1)+'" value="0.00">');                        
                    // $('#paypal-form').append('<input type="hidden" name="shipping" value="'+cartItem.item_handling+'">');
                    }
                    $('#paypal-form').append(' <input type="hidden" name="item_number_'+(index+1)+'" value="'+cartItem.item_number+'">');
                }
                $('#paypal-form').append('<input type="hidden" name="quantity_'+(index+1)+'" value="'+cartItem.item_quantity+'">');
                if(localStorage.getItem('localPickup')!='true'){
                    if(handlingTotal==0){
                        if(cartItem.item_quantity>1){
                            cartItemHandling = +cartItem.item_handling + (cartItem.item_quantity-1)*cartItem.item_handling/2;
                        }else{ 
                            cartItemHandling+= +cartItem.item_handling;
                        }
                    }else{
                        cartItemHandling+= +(cartItem.item_handling/2)*(cartItem.item_quantity);
                    }
                }else{
                    if(localStorage.getItem('pickupType')=='normal'){
                        cartItemHandling = 0;
                    }else{
                        if(index==0){

                            cartItemHandling = 10;
                        }
                        else{
                            cartItemHandling = 0;
                        }
                    }
                    // cartItemHandling = 0;
                }
                $('#paypal-form').append('<input type="hidden" name="handling_'+(index+1)+'" value="'+cartItemHandling.toFixed(2)+'">');
               handlingTotal+=cartItemHandling;

              subTotal+= +cartItem.item_price*cartItem.item_quantity;
              let $itemDetails = ''
      
               for(var i=0;i<10;i++){
                    if(cartItem['item_on'+i] && cartItem['item_on'+i].length>0){
                        $itemDetails += '<p>'+cartItem['item_on'+i] + ':' + cartItem['item_os'+i] + '</p>';                      
                    }
                }
            $itemDetails+='<p class="summary-cart-item-number">Item:'+cartItem.item_number +'</p>';
            let $row = $('<div class="summary-cart-item"></div>');
            let $itemName = $('<span></span>').html('<p>'+cartItem.item_name+'</p>'+$itemDetails); 
            let $itemPerPrice =  $('<span></span>').html('$'+(+cartItem.item_price).toFixed(2));
            let $itemPrice = $('<span></span>').html('$'+(cartItem.item_price*cartItem.item_quantity).toFixed(2)); 
            let $deleteButton = "<span class='delete-button'><img class='delete-button-image' src='images/icons/garbage-can.png' data-item-id='"+index+"'' /></span>";
            let $quantity = "<span class='change-quantity'><span class='decrement-quantity' data-item-id='"+index+"' data-item-count='"+cartItem.item_quantity+"'>-</span><span class='quantity'>"+cartItem.item_quantity+"</span><span class='increment-quantity' data-item-id='"+index+"'>+</span></span>";
            let $content = $row.append($itemName,$quantity, $itemPerPrice, $itemPrice,$deleteButton);
              return $row;
             
            });
            if(localStorage.getItem('localPickup')=='true'){
                if(localStorage.getItem('pickupType')=='normal'){

                    $('#paypal-form').append('<input type="hidden" name="custom" value="LOCAL PICKUP SELECTED">');
                }else{
                    $('#paypal-form').append('<input type="hidden" name="custom" value="PRIORITY PICKUP SELECTED">');
                }
            }
            if(hasExpeditedShipping==true){
                // console.log('has expedited shipping');
                $('.expedited-shipping-container').addClass('has-expedited-shipping');
            }else{
                // console.log('no expedited shipping');
                // $('.expedited-shipping').css('visiblity','visible');
            }
            shippingTotal = calculateShipping(subTotal);
            // console.log('shipping total: '+shippingTotal);
            // $('#paypal-form').append('<input type="hidden" name="shipping" value="'+shippingTotal+'">');
           
            handlingTotal += shippingTotal;
            orderTotal = subTotal+handlingTotal;
            $('#paypal-form').append('<input type="hidden" name="receiver_email" value="sales@centerpinangling.com">')
            $('.totals-section').before(items);
            // $('.shipping-handling-container').before("<div class='summary-sub-total'><span>Sub-total:</span><span> $"+subTotal.toFixed(2)+"</span></div>")
            $('.order-total').html(subTotal.toFixed(2));
            $('#s-h').text('$'+handlingTotal.toFixed(2));
           
            $('#summary').append("");
           
        }else{ 
            $('#shopping-cart').append("<span>The cart is empty</span>");
        }
  
        if(hasRodInCart){
        $('.shipping-canpar .upgrade-price').text('$'+esr.withRod.canpar);
        $('.upgrade-canpar input[name="amount"]').val(esr.withRod.canpar);
        $('.shipping-express .upgrade-price').text('$'+esr.withRod.express);
        $('.upgrade-express input[name="amount"]').val(esr.withRod.express);
        $('.shipping-express-next-canada .upgrade-price').text('$'+esr.withRod.expressNextCanada);
        $('.upgrade-express-next-canada input[name="amount"]').val(esr.withRod.expressNextCanada);
        $('.shipping-express-next-usa .upgrade-price').text('$'+esr.withRod.expressNextUsa);
        $('.upgrade-express-next-usa input[name="amount"]').val(esr.withRod.expressNextUsa);
        $('.shipping-overseas .upgrade-price').text('$'+esr.withRod.overSeas);
        $('.upgrade-overseas input[name="amount"]').val(esr.withRod.overSeas);

    }else{
        $('.shipping-canpar .upgrade-price').text('$'+esr.regular.canpar)
        $('.upgrade-canpar input[name="amount"]').val(esr.regular.canpar)
        $('.shipping-express .upgrade-price').text('$'+esr.regular.express);
        $('.upgrade-express input[name="amount"]').val(esr.regular.express);
        $('.shipping-express-next-canada .upgrade-price').text('$'+esr.regular.expressNextCanada);
        $('.upgrade-express-next-canada input[name="amount"]').val(esr.regular.expressNextCanada);
        $('.shipping-express-next-usa .upgrade-price').text('$'+esr.regular.expressNextUsa);
        $('.upgrade-express-next-usa input[name="amount"]').val(esr.regular.expressNextUsa);
        $('.shipping-overseas .upgrade-price').text('$'+esr.regular.overSeas);
        $('.upgrade-overseas input[name="amount"]').val(esr.regular.overSeas);
    }
      }
     
      processCart();

      $('#country-select').trigger('change');

      // Stripe Payment Element — cards, Apple Pay, Google Pay
      var stripeInstance = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
      var stripeElements = null;
      var stripePaymentEl = null;
      var stripeAddressEl = null;
      var stripeClientSecret = null;

      async function initStripePayment(totalInDollars) {
        if (!totalInDollars || totalInDollars <= 0 || isNaN(totalInDollars)) return;
        var amountInCents = Math.round(totalInDollars * 100);
        try {
          var cartItems = (JSON.parse(localStorage.getItem('shopping-cart')) || []).map(function(item) {
            var name = item.item_name || '';
            if (item.item_on0 && item.item_on0.length) name += ', ' + item.item_on0 + ': ' + item.item_os0;
            if (item.item_on1 && item.item_on1.length) name += ', ' + item.item_on1 + ': ' + item.item_os1;
            if (item.item_on2 && item.item_on2.length) name += ', ' + item.item_on2 + ': ' + item.item_os2;
            if (item.item_on3 && item.item_on3.length) name += ', ' + item.item_on3 + ': ' + item.item_os3;
            if (item.item_on4 && item.item_on4.length) name += ', ' + item.item_on4 + ': ' + item.item_os4;
            return { name: name, qty: item.item_quantity, price: item.item_price, sku: item.item_number || '' };
          });

          var orderSubtotal = parseFloat($('.order-total').text()) || 0;
          var orderShipping = parseFloat($('#s-h').text().replace('$', '')) || 0;
          var orderTax      = parseFloat($('#tax-amount').text().replace('$', '')) || 0;
          var orderTariff   = parseFloat($('#tariff-fee').text().replace('$', '')) || 0;

          var pickupSelected = localStorage.getItem('localPickup') === 'true';
          var pickupType     = localStorage.getItem('pickupType') || '';
          var pickupValue    = pickupSelected ? (pickupType === 'normal' ? 'local' : 'priority') : '';

          var resp = await fetch('./session/create_payment_intent.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              amount: amountInCents,
              items: cartItems,
              subtotal: orderSubtotal,
              shipping: orderShipping,
              tax: orderTax,
              tariff: orderTariff,
              pickup: pickupValue
            })
          });
          if (!resp.ok) return;
          var data = await resp.json();
          if (data.error) { console.error(data.error); return; }
          stripeClientSecret = data.clientSecret;

          if (stripeAddressEl) { stripeAddressEl.unmount(); stripeAddressEl = null; }
          if (stripePaymentEl) { stripePaymentEl.unmount(); stripePaymentEl = null; }

          stripeElements = stripeInstance.elements({
            clientSecret: data.clientSecret,
            appearance: { theme: 'stripe', variables: { colorPrimary: '#172F41' } }
          });

          $('#stripe-payment-section').show();

          if (pickupValue) {
            $('#stripe-shipping-heading').hide();
            $('#stripe-address-element').hide().empty();
            $('#stripe-name-field').show();
            $('#stripe-pickup-note').text(
              pickupValue === 'priority' ? 'Priority In-Store Pickup Selected (within 24 hours)' : 'Local Pickup Selected'
            ).show();
            stripeAddressEl = null;
          } else {
            $('#stripe-shipping-heading').show();
            $('#stripe-address-element').show();
            $('#stripe-name-field').hide();
            $('#stripe-pickup-note').hide().text('');
            try {
              stripeAddressEl = stripeElements.create('address', { mode: 'shipping' });
              stripeAddressEl.mount('#stripe-address-element');
            } catch (addrErr) {
              console.error('Stripe Address Element error:', addrErr);
              stripeAddressEl = null;
            }
          }

          stripePaymentEl = stripeElements.create('payment', {
            terms: { card: 'never', applePay: 'never', googlePay: 'never' },
            fields: { billingDetails: { address: { postalCode: pickupValue ? 'auto' : 'never' } } },
            wallets: { link: 'never', applePay: 'never', googlePay: 'never' }
          });
          stripePaymentEl.mount('#stripe-payment-element');
        } catch (err) {
          console.error('Stripe init error:', err);
        }
      }

      // Fire after app.js handlers (bound first) have updated #total-amount-with-tax
      $('#tax-country').on('change', function () {
        var total = parseFloat($('#total-amount-with-tax').text().replace('$', ''));
        if (total > 0 && !isNaN(total)) {
          initStripePayment(total);
        } else {
          $('#stripe-payment-section').hide();
        }
      });

      $('#tax-province').on('change', function () {
        var total = parseFloat($('#total-amount-with-tax').text().replace('$', ''));
        if (total > 0 && !isNaN(total)) { initStripePayment(total); }
      });

      $('#stripe-phone').on('input', function () {
        var digits = $(this).val().replace(/\D/g, '').substring(0, 10);
        var formatted = digits;
        if (digits.length > 6) {
          formatted = '(' + digits.substring(0, 3) + ') ' + digits.substring(3, 6) + '-' + digits.substring(6, 10);
        } else if (digits.length > 3) {
          formatted = '(' + digits.substring(0, 3) + ') ' + digits.substring(3, 6);
        } else if (digits.length > 0) {
          formatted = '(' + digits;
        }
        $(this).val(formatted);
      });

      $('#stripe-submit-btn').on('click', async function () {
        if (!stripeElements) return;
        var $btn = $(this);
        $btn.prop('disabled', true).text('Processing...');
        $('#stripe-error-msg').hide();

        var email = $('#stripe-email').val().trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          $('#stripe-error-msg').text('Please enter a valid email address.').show();
          $btn.prop('disabled', false).text('Pay Now');
          return;
        }
        var phone = $('#stripe-phone').val().trim();
        if (!phone) {
          $('#stripe-error-msg').text('Please enter your phone number.').show();
          $btn.prop('disabled', false).text('Pay Now');
          return;
        }

        var pickupSelected = localStorage.getItem('localPickup') === 'true';
        var pickupType     = localStorage.getItem('pickupType') || '';
        var pickupValue    = pickupSelected ? (pickupType === 'normal' ? 'local' : 'priority') : '';

        var name = $('#stripe-name').val().trim();
        if (pickupValue && !name) {
          $('#stripe-error-msg').text('Please enter your full name.').show();
          $btn.prop('disabled', false).text('Pay Now');
          return;
        }

        // No receipt_email: Stripe would email its own receipt regardless of the
        // dashboard setting. Email goes into intent metadata for the webhook instead.
        var confirmParams = { return_url: '<?php echo STRIPE_SUCCESS_URL; ?>' };

        try {
          await fetch('./session/set_intent_email.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(pickupValue
              ? { client_secret: stripeClientSecret, email: email, name: name, phone: phone, pickup: pickupValue }
              : { client_secret: stripeClientSecret, email: email })
          });
        } catch (e) {
          console.error('Could not attach email to payment intent:', e);
        }

        if (!pickupValue) {
          var addressElement = stripeElements.getElement('address');
          var addrResult = await addressElement.getValue();
          if (!addrResult.complete) {
            $('#stripe-error-msg').text('Please complete the shipping address.').show();
            $btn.prop('disabled', false).text('Pay Now');
            return;
          }
          var addrValue = addrResult.value;
          confirmParams.shipping = {
            name: addrValue.name,
            phone: phone,
            address: {
              line1: addrValue.address.line1,
              line2: addrValue.address.line2 || null,
              city: addrValue.address.city,
              state: addrValue.address.state || null,
              postal_code: addrValue.address.postal_code,
              country: addrValue.address.country
            }
          };
          confirmParams.payment_method_data = {
            billing_details: {
              address: { postal_code: addrValue.address.postal_code }
            }
          };
        }

        var result = await stripeInstance.confirmPayment({
          elements: stripeElements,
          confirmParams: confirmParams
        });

        if (result.error) {
          $('#stripe-error-msg').text(result.error.message).show();
          $btn.prop('disabled', false).text('Pay Now');
        }
      });

    });
    </script>
</head>
<body
    bgcolor="#172F41"
    LEFTMARGIN="0"
    TOPMARGIN="0"
    MARGINWIDTH="0"
    MARGINHEIGHT="0"
  >
  <table width="1080" border="0" cellpadding="0" cellspacing="0" style="margin:0 auto;background-color:white">
  <tr>
    <td colspan="4">
        <div class="site-menu">
        
           <a href="index.html">Home</a>
           <a href="about.html">About Us</a>
           <a href="gallery.html">Gallery</a>
           <a href="faqs.html">FAQs</a>
           <a href="contact.html">Contact Us</a>
           <a href="sitemap.html">Site Map</a>
           <div class="search-container"> <label class="search-label">Search</label><script async src="https://cse.google.com/cse?cx=55c1a335417a54bca"></script>
                        <div class="gcse-search" enablehistory="false"></div>
                    </div>
    </div>
        </td>
</tr>
   
<tr>
            <td colspan=4>
                <div class="banner-div" id="banner"></div>
            </td>
        </tr>
 <tr>
 <td  style="background:rgb(11,23,32);text-align:center;color:white;padding-top:1.5em;padding-bottom:.5em;font-size:20px;font-weight:bold">Categories</td>
 </tr>
    <tr>
      <td rowspan="5" valign="top"  style="background:rgb(11,23,32)">
    
          <table width="90%" border="0" cellspacing="2" cellpadding="0" class="category-nav">
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#FFFFFF"
                    ><a href="centerpinreels.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                        >Centerpin Reels</font
                      ></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="float-rods.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                        >Float Rods
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="backing-line.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Backing Line</font
                      ></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="main-line.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                        >Main Line</font
                      ></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="leader-line.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                        >Leader Line/Tippet
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="floats.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Floats
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="swivels.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Swivels
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="split-shot.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Split Shot
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="hooks.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Hooks
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="spawn-products.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Spawn Products
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="egg-yarn-files.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Egg/Yarn Flies
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="steelhead-flies.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Steelhead Jigs</font
                    ></a
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="rod&reel-combos.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Rod &amp; Reel Combos
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="power-bait.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                        >Power bait / pink worm</font
                      ></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="accessories.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Accessories
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="waders.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Waders
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="parts_maintenance.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                        >Parts &amp; Bearings</font
                      ></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="apparel.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Apparel
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="miscelaneous-items.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Miscellaneous Items
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="fly-boxes.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Fly Boxes</font
                      ></a
                    ><a href="sale-items.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                        ><br /> </font></a
                  ></font>
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top"> 
            <td> <div align="right"><font color="#CDEAFF"><a href="cac_custom_rods.html"><font size="-2" face="Verdana, Arial, Helvetica, sans-serif"> 
              Blanks & Rod Building</font></a></font></div></td>
            <td><img src="images/bullet.png" width="10" height="14"></td>
          </tr>
          <tr valign="top"> 
            <td> <div align="right"><font color="#CDEAFF"><a href="reel_spooling_service.html"><font size="-2" face="Verdana, Arial, Helvetica, sans-serif"> 
              Reel Spooling Service </font></a><a href="sale-items.html"><font size="-2" face="Verdana, Arial, Helvetica, sans-serif"><br>
                </font></a></font></div></td>
            <td><img src="images/bullet.png" width="10" height="14"></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="fly-tying.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Fly Tying Supplies
                    </font></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="steelhead-beads.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Steelhead Beads</font
                      ></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="packs-vests-bags.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Packs, Vests & Bags</font
                      ></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td>
              <div align="right">
                <font color="#CDEAFF"
                    ><a href="waders-boots-footwear.html"
                      ><font
                        size="-2"
                        face="Verdana, Arial, Helvetica, sans-serif"
                      >
                      Wading Boots & Footwear</font
                      ></a
                    ></font
                  >
              </div>
            </td>
            <td><img src="images/bullet.png" width="10" height="14" /></td>
          </tr>
          <tr valign="top">
            <td><font color="#CDEAFF">&nbsp;</font></td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
     
    </tr>
    <tr>
      <td rowspan="4" valign="top" style="background:white">
        <div align="center" id="checkout-form">
     
            <fieldset class="order-summary">
                <legend>Order Summary</legend>
                        <div id="summary">
                            <div class="summary-heading">
                                <span>Item Name</span><span>Quantity</span><span>Price</span><span>Sub-total</span>
                            </div>
                            <div class="totals-section">
                               
                                <div class="shipping-details">
                                    <div class="local-pickup">
                                 <span>Prefer in-store pick-up? Check the box to the right (no charge applies)</span>
                                 <input type="checkbox" name="local-pickup" value="normal" id="local-pickup" class="local">
                                                                </div>
                                                                <div class="priority-pickup">
                                 <span>Prefer priority in-store pick-up (within 24 hours)? Check the box to the right.</span>
                                 <input type="checkbox" name="priority-pickup" value="priority" id="priority-pickup" class="local">
                                                                </div>
                                    <div class="expedited-shipping-container">
                                    <div class="expedited-heading">Going on a steelhead trip? You can add expedited shipping to your order to get as fast as next day delivery (in addition to the shipping & handling rate).</div>
                                    <span class='expedited-button'>View Rates</span>
                                    <div class="expedited-shipping-rates">
                                        <h3>Expedited Shipping Upgrade Options</h3>
                                        <div class="shipping-canpar shipping-rate">
                                            <div class='rate-left'>
                                            <div class='shipping-rate-heading'>Courier Ground Service <span class="upgrade-price"></span></div>
                                            <span class='shipping-rate-text'>Canada:1-5 Business days, USA (L-48):3-7 Business days</span>
                                            </div>
                                            <form class='shipping-upgrade upgrade-canpar' target="_self" action="./session/session.php" method="post">
                                                <input type="hidden" name="cmd" value="_cart">
                                                <input type="hidden" name="add" value="1">
                                                <input type="hidden" name="bn" value="webassist.dreamweaver.4_0_1">
                                                <input type="hidden" name="business" value="sales@centerpinangling.com">
                                                <input type="hidden" name="item_name" value="Courier Ground">
                                                <input type="hidden" name="item_number" value="es-cg">
                                                <input type="hidden" name="amount" value="14.99">
                                                <input type="hidden" name="currency_code" value="CAD">
                                                <input type="hidden" name="receiver_email" value="sales@centerpinangling.com">
                                                <input type="hidden" name="mrb" value="R-3WH47588B4505740X">
                                                <input type="hidden" name="pal" value="ANNSXSLJLYR2A">
                                                <input type="hidden" name="shipping" value="0.0">
                                                <input type="hidden" name="no_note" value="0">
                                                <input type="hidden" name="return" value="http://www.centerpinangling.com/thanks.html">
                                                <input type="hidden" name="cancel_return" value="http://www.centerpinangling.com/cancel.html">
                                                <input type="submit" class='submit-button' value="Add to cart">
                                            </form>
                                        </div>
                                        <div class="shipping-express shipping-rate">
                                            <div class='rate-left'>
                                            <div class='shipping-rate-heading'>Express Post Service <span class="upgrade-price"></span></div>
                                            <span class='shipping-rate-text'>Canada:1-4 Business days, USA (L-48):2-5 Business days</span>
                                            </div>
                                            <form class='shipping-upgrade upgrade-express' target="_self" action="./session/session.php" method="post">
                                                <input type="hidden" name="cmd" value="_cart">
                                                <input type="hidden" name="add" value="1">
                                                <input type="hidden" name="bn" value="webassist.dreamweaver.4_0_1">
                                                <input type="hidden" name="business" value="sales@centerpinangling.com">
                                                <input type="hidden" name="item_name" value="Express Post">
                                                <input type="hidden" name="item_number" value="es-ep">
                                                <input type="hidden" name="amount" value="14.99">
                                                <input type="hidden" name="currency_code" value="CAD">
                                                <input type="hidden" name="receiver_email" value="sales@centerpinangling.com">
                                                <input type="hidden" name="mrb" value="R-3WH47588B4505740X">
                                                <input type="hidden" name="pal" value="ANNSXSLJLYR2A">
                                                <input type="hidden" name="shipping" value="0.0">
                                                <input type="hidden" name="no_note" value="0">
                                                <input type="hidden" name="return" value="http://www.centerpinangling.com/thanks.html">
                                                <input type="hidden" name="cancel_return" value="http://www.centerpinangling.com/cancel.html">
                                                <input type="submit" class='submit-button' value="Add to cart">
                                            </form>
                                        </div>
                                        <div class="shipping-express-next-canada shipping-rate">
                                            <div class='rate-left'>
                                            <div class='shipping-rate-heading'>Canada Express Next Day<span class="upgrade-price"></span></div>
                                            <span class='shipping-rate-text'>Delivery Next day - most points</span>
                                            </div>
                                            <form class='shipping-upgrade upgrade-express-next-canada' target="_self" action="./session/session.php" method="post">
                                                <input type="hidden" name="cmd" value="_cart">
                                                <input type="hidden" name="add" value="1">
                                                <input type="hidden" name="bn" value="webassist.dreamweaver.4_0_1">
                                                <input type="hidden" name="business" value="sales@centerpinangling.com">
                                                <input type="hidden" name="item_name" value="Express Next Canada">
                                                <input type="hidden" name="item_number" value="es-enc">
                                                <input type="hidden" name="amount" value="14.99">
                                                <input type="hidden" name="currency_code" value="CAD">
                                                <input type="hidden" name="receiver_email" value="sales@centerpinangling.com">
                                                <input type="hidden" name="mrb" value="R-3WH47588B4505740X">
                                                <input type="hidden" name="pal" value="ANNSXSLJLYR2A">
                                                <input type="hidden" name="shipping" value="0.0">
                                                <input type="hidden" name="no_note" value="0">
                                                <input type="hidden" name="return" value="http://www.centerpinangling.com/thanks.html">
                                                <input type="hidden" name="cancel_return" value="http://www.centerpinangling.com/cancel.html">
                                                <input type="submit" class='submit-button' value="Add to cart">
                                            </form>
                                        </div>
                                        <div class="shipping-express-next-usa shipping-rate">
                                            <div class='rate-left'>
                                            <div class='shipping-rate-heading'>USA Express Next Day<span class="upgrade-price"></span></div>
                                            <span class='shipping-rate-text'>Delivery Next day - most points</span>
                                            </div>
                                            <form class='shipping-upgrade upgrade-express-next-usa' target="_self" action="./session/session.php" method="post">
                                                <input type="hidden" name="cmd" value="_cart">
                                                <input type="hidden" name="add" value="1">
                                                <input type="hidden" name="bn" value="webassist.dreamweaver.4_0_1">
                                                <input type="hidden" name="business" value="sales@centerpinangling.com">
                                                <input type="hidden" name="item_name" value="Express Next USA">
                                                <input type="hidden" name="item_number" value="es-enu">
                                                <input type="hidden" name="amount" value="14.99">
                                                <input type="hidden" name="currency_code" value="CAD">
                                                <input type="hidden" name="receiver_email" value="sales@centerpinangling.com">
                                                <input type="hidden" name="mrb" value="R-3WH47588B4505740X">
                                                <input type="hidden" name="pal" value="ANNSXSLJLYR2A">
                                                <input type="hidden" name="shipping" value="0.0">
                                                <input type="hidden" name="no_note" value="0">
                                                <input type="hidden" name="return" value="http://www.centerpinangling.com/thanks.html">
                                                <input type="hidden" name="cancel_return" value="http://www.centerpinangling.com/cancel.html">
                                                <input type="submit" class='submit-button' value="Add to cart">
                                            </form>
                                        </div>
                                        <!-- <div class="shipping-overseas shipping-rate">
                                            <div class='rate-left'>
                                            <div class='shipping-rate-heading'>Overseas Express Air<span class="upgrade-price"></span></div>
                                            <span class='shipping-rate-text'>3-8 Business days - most points</span>
                                            </div>
                                            <form class='shipping-upgrade upgrade-overseas' target="_self" action="./session/session.php" method="post">
                                                <input type="hidden" name="cmd" value="_cart">
                                                <input type="hidden" name="add" value="1">
                                                <input type="hidden" name="bn" value="webassist.dreamweaver.4_0_1">
                                                <input type="hidden" name="business" value="sales@centerpinangling.com">
                                                <input type="hidden" name="item_name" value="Express Overseas">
                                                <input type="hidden" name="item_number" value="es-ov">
                                                <input type="hidden" name="amount" value="14.99">
                                                <input type="hidden" name="currency_code" value="CAD">
                                                <input type="hidden" name="receiver_email" value="sales@centerpinangling.com">
                                                <input type="hidden" name="mrb" value="R-3WH47588B4505740X">
                                                <input type="hidden" name="pal" value="ANNSXSLJLYR2A">
                                                <input type="hidden" name="shipping" value="0.0">
                                                <input type="hidden" name="no_note" value="0">
                                                <input type="hidden" name="return" value="http://www.centerpinangling.com/thanks.html">
                                                <input type="hidden" name="cancel_return" value="http://www.centerpinangling.com/cancel.html">
                                                <input type="submit" class='submit-button' value="Add to cart">
                                            </form>
                                        </div> -->
                                    </div>
                                    </div>
                                </div>
                                <div class="summary-section">
                                  <!--<div class="summary-discount-code">
                                    <span>Have a discount code?</span>
                                    <input type="text" name="discount_code" id="discount_code" >
                                  </div>-->
                                  <div class='summary-subtotal'>
                                    <span>Sub-Total:</span><span> $<span class='order-total'></span> </span>
                                  </div>
                                    <div class="shipping-handling-container sh-hidden">
                                        <span>S&H:</span>
                                        <span id="s-h"></span>
                                    </div>
                                    <!--<div class="us-tariff sh-hidden">
                                      <span>Tariff Fees:</span><span id="tariff"></span>
                                    </div>-->
                                    <!-- <div class='tax-and-shipping-container'>
                                        <div class='tax-container'><span class='tax-amount'>Taxes calculated on paypal payment page</span></div>
                                    </div> -->
                                    <div class="summary-tax-country-container">
                                      
                                        <span>Shipping Country:</span>
                                        <select name="tax-country" id="tax-country">
                                          <option value="">Select Your Country</option>
                                          <option value="CA">Canada</option>
                                          <option value="US">United States</option>
                                          <option value="OT">Other</option>
                                        </select>
                                        
                                      </div>
                                      <div class="summary-tax-province-container province-hidden">
                                      <span>Province:</span>
                                    <select name="tax-province" id="tax-province">
                                      <option value="">Select Province</option>
                                      <option value="AB">Alberta</option>
                                      <option value="BC">British Columbia</option>
                                      <option value="MB">Manitoba</option>
                                      <option value="NB"k">New Brunswick</option>
                                      <option value="NL">Nfld & Labrador</option>
                                      <option value="NW">N.W.T.</option>
                                      <option value="NS">Nova Scotia</option>
                                      <option value="NN">Nunavut</option>
                                      <option value="ON">Ontario</option>
                                      <option value="PE">P.E.I.</option>
                                      <option value="QC">Quebec</option>
                                      <option value="SK">Saskatchewan</option>
                                      <option value="YK">Yukon</option>
                                     </select>
                                    </div>
                                    <div class="summary-tax-container">
                                      <span>Taxes:</span>
                                      <span id="tax-amount"></span>
                                    </div>
                                    <div class="summary-total">
                                      <span>Total:</span>
                                      <span id='total-amount-with-tax'></span>
                                    </div>
                                    <input type="hidden" id="rodincart" value="0">
                                    <input type="hidden" id="rodincartUS" value="0">
                                    <div class="paypal-button-container">
                                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" id='paypal-form' target="_self">
                                            <input type="hidden" name="cmd" value="_cart">
                                            <input type="hidden" name="upload" value="1">
                                            <input type="hidden" name="currency_code" value="CAD">
                                            <input type="hidden" name="business" value="sales@centerpinangling.com">
                                            <input type="hidden" name="tax" value="0" id="tax-amount-hidden">
                                            <input type="hidden" name="tax_rate" value="0.5">
                                            <input type="hidden" name="tax_cart" value="0" id="tax-cart">
                                            <button class='checkout-button  paypal-checkout-button paypal-button-disabled' disabled></button>

                                            <!-- <a href="tets">pay</a> -->
                                            <!-- <input type="image" name="submit" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png" border="0" alt="Submit"  class='paypal-button'/> -->
                                        </form>
                                        <!--<p><span class="tariff_warning">NOTE</span>: US and International orders may be subject to tariffs and border fees.</p>-->
                                    </div>

                                    <div id="stripe-payment-section" style="display:none;" class="stripe-form-section">
                                        <!--<div class="stripe-divider">or pay with card &sol; Apple Pay &sol; Google Pay</div>-->
                                        <div class="stripe-divider">or pay with credit card </div>

                                        <div class="stripe-field">
                                            <label for="stripe-email" class="stripe-field-label">Email <span class="stripe-required">*</span></label>
                                            <input type="email" id="stripe-email" placeholder="you@example.com" class="stripe-input">
                                        </div>

                                        <div class="stripe-field">
                                            <label for="stripe-phone" class="stripe-field-label">Phone Number <span class="stripe-required">*</span></label>
                                            <input type="tel" id="stripe-phone" placeholder="+1 (555) 555-5555" class="stripe-input">
                                        </div>

                                        <div class="stripe-field" id="stripe-name-field" style="display:none;">
                                            <label for="stripe-name" class="stripe-field-label">Full Name <span class="stripe-required">*</span></label>
                                            <input type="text" id="stripe-name" placeholder="Jane Doe" class="stripe-input">
                                        </div>

                                        <div class="stripe-section-heading" id="stripe-shipping-heading">Shipping Address <span class="stripe-required">*</span></div>
                                        <div id="stripe-address-element"></div>
                                        <div id="stripe-pickup-note" style="display:none; padding:10px 12px; background:#eef6ff; border-left:4px solid #172F41; font-size:13px; color:#172F41; border-radius:4px; margin-bottom:10px;"></div>

                                        <div class="stripe-section-heading">Payment</div>
                                        <div id="stripe-payment-element"></div>
                                        <div id="stripe-error-msg" style="display:none; color:#df1b41; font-size:13px; margin:6px 0;"></div>
                                        <button id="stripe-submit-btn" class="checkout-button stripe-checkout-button" style="margin-top:10px; width:100%;">Pay Now</button>
                                    </div>

                                    <!-- <span class="pay-by-heading">---- Or ----</span> -->
                                    <!-- <div class="credit-card-container">
                                        <div style="display: flex;justify-content: space-around; width: 40%;margin: 0 auto;" class='credit-card-payment'>
                                            <img src="https://www.paypal.com/en_US/i/logo/logo_ccVisa.gif" alt=" Visa" border=0>
                                            <img src="https://www.paypal.com/en_US/i/logo/logo_ccMC.gif" alt=" Mastercard" border=0>
                                            <img src="https://www.paypal.com/en_US/i/logo/logo_ccDiscover.gif" border=0 alt=" Discover">
                                            <img src="https://www.paypal.com/en_US/i/logo/logo_ccAmex.gif" border=0 alt=" American Express">
                                        </div>
                                        <div class="credit-card-info">
                                            <div class="credit-card-info-heading">Credit Card Information</div>
                                            <div class="credit-card-info-text">We accept Visa, Mastercard, Discover and American Express</div>
                                            <div class="credit-card-details">
                                            <form>
                                                <label for="cardholder-name">Cardholder Name:</label>
                                                <input type="text" id="cardholder-name" name="cardholder-name" required>

                                                <label for="card-number">Card Number:</label>
                                                <input type="text" id="card-number" name="card-number" required>

                                                <label for="expiration-date">Expiration Date:</label>
                                                <input type="month" id="expiration-date" name="expiration-date" required>

                                                <label for="cvv">CVV:</label>
                                                <input type="text" id="cvv" name="cvv" required>

                                                <label for="billing-address">Billing Address:</label>
                                                <input type="text" id="billing-address" name="billing-address" required>

                                                <label for="city">City:</label>
                                                <input type="text" id="city" name="city" required>

                                                <label for="state">State/Province:</label>
                                                <select id="state" name="state">
                                                    <option value="AB">Alberta</option>
                                                    <option value="BC">British Columbia</option>
                                                    <option value="MB">Manitoba</option>
                                                    <option value="NB">New Brunswick</option>
                                                    <option value="NL">Newfoundland and Labrador</option>
                                                    <option value="NT">Northwest Territories</option>
                                                    <option value="NS">Nova Scotia</option>
                                                    <option value="NU">Nunavut</option>
                                                    <option value="ON">Ontario</option>
                                                    <option value="PE">Prince Edward Island</option>
                                                    <option value="QC">Quebec</option>
                                                    <option value="SK">Saskatchewan</option>
                                                    <option value="YT">Yukon</option>
                                                    <option disabled >-----------------</option>
                                                    <option value="AL">Alabama</option>
                                                    <option value="AK">Alaska</option>
                                                    <option value="AZ">Arizona</option>
                                                    <option value="AR">Arkansas</option>
                                                    <option value="CA">California</option>
                                                    <option value="CO">Colorado</option>
                                                    <option value="CT">Connecticut</option>
                                                    <option value="DE">Delaware</option>
                                                    <option value="DC">District Of Columbia</option>
                                                    <option value="FL">Florida</option>
                                                    <option value="GA">Georgia</option>
                                                    <option value="HI">Hawaii</option>
                                                    <option value="ID">Idaho</option>
                                                    <option value="IL">Illinois</option>
                                                    <option value="IN">Indiana</option>
                                                    <option value="IA">Iowa</option>
                                                    <option value="KS">Kansas</option>
                                                    <option value="KY">Kentucky</option>
                                                    <option value="LA">Louisiana</option>
                                                    <option value="ME">Maine</option>
                                                    <option value="MD">Maryland</option>
                                                    <option value="MA">Massachusetts</option>
                                                    <option value="MI">Michigan</option>
                                                    <option value="MN">Minnesota</option>
                                                    <option value="MS">Mississippi</option>
                                                    <option value="MO">Missouri</option>
                                                    <option value="MT">Montana</option>
                                                    <option value="NE">Nebraska</option>
                                                    <option value="NV">Nevada</option>
                                                    <option value="NH">New Hampshire</option>
                                                    <option value="NJ">New Jersey</option>
                                                    <option value="NM">New Mexico</option>
                                                    <option value="NY">New York</option>
                                                    <option value="NC">North Carolina</option>
                                                    <option value="ND">North Dakota</option>
                                                    <option value="OH">Ohio</option>
                                                    <option value="OK">Oklahoma</option>
                                                    <option value="OR">Oregon</option>
                                                    <option value="PA">Pennsylvania</option>
                                                    <option value="RI">Rhode Island</option>
                                                    <option value="SC">South Carolina</option>
                                                    <option value="SD">South Dakota</option>
                                                    <option value="TN">Tennessee</option>
                                                    <option value="TX">Texas</option>
                                                    <option value="UT">Utah</option>
                                                    <option value="VT">Vermont</option>
                                                    <option value="VA">Virginia</option>
                                                    <option value="WA">Washington</option>
                                                    <option value="WV">West Virginia</option>
                                                    <option value="WI">Wisconsin</option>
                                                    <option value="WY">Wyoming</option>
                                                </select>

                                                <label for="zip-code">Zip Code:</label>
                                                <input type="text" id="zip-code" name="zip-code" required>

                                                <input type="submit" value="Submit">
                                            </form>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                                
                            </div>
                        </div>
                    <!--    <div id="pay-button">
                            <button class="button-checkout" id="payButton"><div class="spinner hidden" id="spinner></div><span id="buttonText">Pay now</span></button>
                        </div> -->
                        
                   
            </fieldset>
           
        </div>          
      
      </td>
                        </tr>
      <td><img src="images/spacer.gif" width="1" height="66" alt="" /></td>
    </tr>
    <tr>
      <td><img src="images/spacer.gif" width="1" height="41" alt="" /></td>
    </tr>
    <tr>
      <td><img src="images/spacer.gif" width="1" height="295" alt="" /></td>
    </tr>
   
    <tr>
    <td colspan="4">
    <div class="site-footer">
        <div>
            <span>Local Business Hours: Tuesday to Friday 12pm-7pm</span>
            <span>Customer Service:<a href="tel:18447596153" class="footer-link">1-844-759-6153</a> ( Toll free USA / CANADA )</span>
            <span>Online Ordering 24&sol;7</span>
        </div>
        <div class="logo"><img src="./images/fish-logo.png" style="height:100px"></div>
        <div>
                <span> <a href="parts_maintenance.html" style="color:white">Professional Centerpin Reel Repair - Parts &amp; Service</a></span>
                 <span>Call <a href="tel:18447596153" class="footer-link">1-844-759-6153 </a>Toll Free USA &amp; CANADA</span>
            </div>
        </div>
           
    </td>
    </tr>
  </table>
  
  
<div class="cart-modal" id="cart-modal"><div>Item added to cart.</div></div>
<div id="layoutViewport"></div>
</body>
</html>