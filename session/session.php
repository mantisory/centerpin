<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_POST['func'])){
    $function  = $_POST['func'];
    if($function =='removeItem'){
        removeItem($_POST['itemId']);
        echo json_encode($_SESSION['shoppingCart']);
    }elseif($function=='increment'){
        $itemNumber = intval($_POST['itemNumber']);
        changeQuantity($itemNumber,'add');
        echo json_encode($_SESSION['shoppingCart']);
        
    }elseif($function=='decrement'){
      
        $itemNumber = intval($_POST['itemNumber']);
        changeQuantity($itemNumber,'subtract');
        echo json_encode($_SESSION['shoppingCart']);
    }elseif($function == 'changeCountry'){
     
        $_SESSION['countryCode'] = $_POST['countryCode'];
        echo json_encode('changed country');
    }elseif($function == 'changeProvince'){
           
        $_SESSION['provinceCode'] = $_POST['provinceCode'];
        echo 'changed province';
    }elseif($function =='setSession'){
        $_SESSION['shoppingCart'] = json_decode($_POST['cartData']);
        echo 'done';
    }elseif($function =='destroyCart'){
        session_unset();
        echo 'cart destroyed';
    }
   
}else{
    $newItem = createItemObject($_POST);
    $duplicate=false;
    if(isset($_SESSION['shoppingCart']) ){

        $shoppingCart = $_SESSION['shoppingCart'];
        if(count($shoppingCart)>0){
            foreach($shoppingCart as $item){

                if($newItem->item_name == $item->item_name && $newItem->item_number==$item->item_number){
                   if($newItem->item_os0==$item->item_os0 && $newItem->item_os1==$item->item_os1 && $newItem->item_os2==$item->item_os2 && $newItem->item_os3==$item->item_os3 && $newItem->item_os4==$item->item_os4 ){

                            $duplicate=true;
                            $item->item_quantity +=1;
                            break;
                        }
                    }
                }
            }
        
        if(!$duplicate){
            
            array_push($shoppingCart,$newItem);
        }
        $_SESSION['shoppingCart'] = $shoppingCart;
        // var_dump($shoppingCart);
    }else{
        $shoppingCart = array($newItem);
        $_SESSION['shoppingCart'] = $shoppingCart;
        // var_dump($shoppingCart);
    }
echo json_encode($shoppingCart);
}

function changeCountry($country){

}
function changeProvince($province){

}

function changeQuantity($item, $add){
    $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    $txt = $add;
    fwrite($myfile, $txt);
   
   
    $cart = $_SESSION['shoppingCart'];
    $itemToChange = $cart[$item];
    $quantity = $itemToChange-> item_quantity;

    if($add==='add'){
        fwrite( $myfile,'add');
        $itemToChange-> item_quantity += 1;
    }else{
        $itemToChange-> item_quantity -= 1;
        // // fwrite( $myfile,'subtract, quantity:'.$quantity>1?'true':'false');
        // if($quantity>1){
        //     fwrite($myFile, 'subtracting, quantity:');
        //     $itemToChange-> item_quantity -= 1;
        // }          
    }
    fclose($myfile);
    $cart[$item] = $itemToChange;
    $_SESSION['shoppingCart'] = $cart;
    return ;
}

function createItemObject($item){
    
   
    $item_price = $item['amount'];
    
    if(isset( $item['shipping2'])){
        $item_shipping2 = $item['shipping2'];
    }else{
        $item_shipping2='';
    }
    if(isset($item['item_number'])){
        $item_number = $item['item_number'];
    }else{
        $item_number='';
    }
    $item_name = $item['item_name'];
    if(isset($item['handling'])){
        $item_handling = $item['handling'];
    }else{
        $item_handling='';
    }
    $item_options = array();
    $item_on0='';
    $item_os0='';
    $item_on1='';
    $item_os1='';
    $item_on2='';
    $item_os2='';
    $item_on3='';
    $item_os3='';
    $item_on4='';
    $item_os4='';
    



    if(isset($item['on0'])){
        $item_on0=$item['on0'];
        // $item_os0=$item['os0'];
        
        if(isset($item['item_model_0'])){
            $item_os0=$item['item_model_0'];
            array_push($item_options,$item['on0'].':'.$item['item_model_0']);
        }else{
            $item_os0=$item['os0'];
            array_push($item_options,$item['on0'].':'.$item['os0']);
        }
    }
    if(isset($item['on1'])){
        $item_on1=$item['on1'];
       
        if(isset($item['item_model_1'])){
            $item_os1=$item['item_model_1'];
            array_push($item_options,$item['on1'].':'.$item['item_model_1']);
        }else{
            $item_os1=$item['os1'];
            array_push($item_options,$item['on1'].':'.$item['os1']);
        }
    }
    if(isset($item['on2'])){
        $item_on2=$item['on2'];
       
        if(isset($item['item_model_2'])){
            $item_os2=$item['item_model_2'];
            array_push($item_options,$item['on2'].':'.$item['item_model_2']);
        }else{
            $item_os2=$item['os2'];
            array_push($item_options,$item['on2'].':'.$item['os2']);
        }
    }
    if(isset($item['on3'])){
        $item_on3=$item['on3'];
       
        if(isset($item['item_model_3'])){
            $item_os3=$item['item_model_3'];
            array_push($item_options,$item['on3'].':'.$item['item_model_3']);
        }else{
            $item_os3=$item['os3'];
            array_push($item_options,$item['on3'].':'.$item['os3']);
        }
    }
    if(isset($item['on4'])){
        $item_on4=$item['on4'];
       
        if(isset($item['item_model_4'])){
            $item_os4=$item['item_model_4'];
            array_push($item_options,$item['on4'].':'.$item['item_model_4']);
        }else{
            $item_os4=$item['os4'];
            array_push($item_options,$item['on4'].':'.$item['os4']);
        }
    }
 
    // $newItem = array($item_name,$item_price,$item_handling,$item_options);
$newItem = (object)[
    'item_name'=>$item_name,
    'item_price'=>$item_price,
    'item_options'=>$item_options,
    'item_handling'=>$item_handling,
    'item_number'=>$item_number,
    'item_on0'=>$item_on0,
    'item_os0'=>$item_os0,
    'item_on1'=>$item_on1,
    'item_os1'=>$item_os1,
    'item_on2'=>$item_on2,
    'item_os2'=>$item_os2,
    'item_on3'=>$item_on3,
    'item_os3'=>$item_os3,
    'item_on4'=>$item_on4,
    'item_os4'=>$item_os4,
    'item_quantity'=>1
];
return $newItem;
}
function removeItem($itemId){
    // $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    $intItemId = intval($itemId);
    // fwrite($myfile, 'in remove item:'.gettype($itemId));
    
    $shoppingCart = $_SESSION['shoppingCart'];
    // return 'test';
    // $shoppingCart = json_decode('['.$_SESSION['shoppingCart'].']');
    unset($shoppingCart[$intItemId]);
   $_SESSION['shoppingCart']= array_values($shoppingCart);
   return 'done';
}

?>