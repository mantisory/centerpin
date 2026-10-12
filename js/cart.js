function purchaseItem() {
  return {
    item_name: "",
    item_price: "",
    item_number: "",
    item_handling: "0.00",
    item_on0: "",
    item_os0: "",
    item_on1: "",
    item_os1: "",
    item_on2: "",
    item_os2: "",
    item_on3: "",
    item_os3: "",
    item_on4: "",
    item_os4: "",
  };
}
function checkCart() {
  if (localStorage.getItem("shopping-cart")) {
    if (JSON.parse(localStorage.getItem("shopping-cart")).length > 0) {
      $(".cart-icon").slideDown();
    } else {
      $(".cart-icon").slideUp();
    }
  }
}
function getCart() {
  return localStorage.getItem("shopping-cart");
}

function addToCart(element) {
  let cart = JSON.parse(localStorage.getItem("shopping-cart")) || [];
  let cartItem = $(element).serializeArray();
  let item = new purchaseItem();
  let itemOptions = [];

  item.item_name = cartItem.find((e) => e.name === "item_name").value;
  item.item_price = cartItem.find((e) => e.name === "amount").value;
  if (cartItem.find((e) => e.name === "item_number")) {
    item.item_number = cartItem.find((e) => e.name === "item_number").value;
  }
  if (cartItem.find((e) => e.name === "handling")) {
    item.item_handling = cartItem.find((e) => e.name === "handling").value;
  }
  if (cartItem.find((e) => e.name === "on0")) {
    item.item_on0 = cartItem.find((e) => e.name === "on0").value;
    if (cartItem.find((e) => e.name === "item_model_0")) {
      item.item_on0 = cartItem.find((e) => e.name === "item_model_0").value;
    } else if (cartItem.find((e) => e.name === "os0")) {
      item.item_os0 = cartItem.find((e) => e.name === "os0").value;
    }
    itemOptions.push(item.item_on0 + ":" + item.item_os0);
  }
  if (cartItem.find((e) => e.name === "on1")) {
    item.item_on1 = cartItem.find((e) => e.name === "on1").value;
    item.item_os1 = cartItem.find((e) => e.name === "os1").value;
    itemOptions.push(item.item_on1 + ":" + item.item_os1);
  }
  if (cartItem.find((e) => e.name === "on2")) {
    item.item_on2 = cartItem.find((e) => e.name === "on2").value;
    item.item_os2 = cartItem.find((e) => e.name === "os2").value;
    itemOptions.push(item.item_on2 + ":" + item.item_os2);
  }

  if (cartItem.find((e) => e.name === "on3")) {
    item.item_on3 = cartItem.find((e) => e.name === "on3").value;
    item.item_os3 = cartItem.find((e) => e.name === "os3").value;
    itemOptions.push(item.item_on3 + ":" + item.item_os3);
  }
  if (cartItem.find((e) => e.name === "on4")) {
    item.item_on4 = cartItem.find((e) => e.name === "on4").value;
    item.item_os4 = cartItem.find((e) => e.name === "os4").value;
    itemOptions.push(item.item_on4 + ":" + item.item_os4);
  }

  item["item_options"] = [itemOptions];
  item["item_quantity"] = 1;

  let duplicate = false;
  cart.forEach((tempItem, index) => {
    if (
      tempItem.item_name == item.item_name &&
      JSON.stringify(tempItem.item_options) ===
        JSON.stringify(item.item_options)
    ) {
      duplicate = true;
      cart[index].item_quantity += 1;
    }
  });
  if (!duplicate) cart.push(item);
  //   cart.push(item);
  localStorage.setItem("shopping-cart", JSON.stringify(cart));
  $(".shopping-cart-link").removeClass("cart-empty");
  $(".shopping-cart-link")
    .find("span")
    .text("View Cart (" + cart.length + " items)");

  //   $.ajax({
  //     url: "./session/session.php",
  //     data: element.serialize(),
  //     type: "POST",
  //   }).done(function (res) {
  //     localStorage.setItem("shopping-cart", res);
  //     $(".shopping-cart-link").removeClass("cart-empty");
  //     $(".shopping-cart-link")
  //       .find("span")
  //       .text("View Cart (" + JSON.parse(res).length + " items)");
  //   });
}
function removeFromCart(itemID) {
  let shoppingCart = JSON.parse(localStorage.getItem("shopping-cart"));
  let itemIndex = shoppingCart.findIndex((item) => item.itemID === itemID);
  shoppingCart.splice(itemIndex, 1);
  localStorage.setItem("shopping-cart", JSON.stringify(shoppingCart));
}
