const products = [
  {
    divClass: "regularHareline",
    colors: [
      { prodID: "ice109", name: "Electric Purple" },
      { prodID: "ice110", name: "Emerald Green" },
      { prodID: "ice133", name: "Flo. Hot Pink" },
      { prodID: "ice134", name: "Fl. Lime Green" },
      { prodID: "ice142", name: "Fl. Yellow" },
      { prodID: "ice153", name: "Gold 			" },
      { prodID: "ice157", name: "Golden Brown" },
      { prodID: "ice177", name: "Gunmetal Grey" },
      { prodID: "ice189", name: "Hot Yellow" },
      { prodID: "ice199", name: "Kingfisher Blue" },
      { prodID: "ice23", name: "Blue Steelie" },
      { prodID: "ice232", name: "Midnight Fire" },
      { prodID: "ice233", name: "Midnight Haze" },
      { prodID: "ice234", name: "Minnow Belly" },
      { prodID: "ice235", name: "Minnow Pearl Back" },
      { prodID: "ice263", name: "Olive" },
      { prodID: "ice265", name: "Olive Brown" },
      { prodID: "ice271", name: "Orange" },
      { prodID: "ice282", name: "Peacock" },
      { prodID: "ice283", name: "Peacock Black" },
      { prodID: "ice284", name: "Pearl" },
      { prodID: "ice286", name: "Pearl Red Hue" },
      { prodID: "ice288", name: "Pheasant Tail" },
      { prodID: "ice298", name: "Purple" },
      { prodID: "ice300", name: "Red" },
      { prodID: "ice323", name: "Rusty Brown" },
      { prodID: "ice340", name: "Flo. Shell Pink" },
      { prodID: "ice344", name: "Silver Holographic" },
      { prodID: "ice345", name: "Silver " },
      { prodID: "ice350", name: "Silver Grey" },
      { prodID: "ice359", name: "Smolt Blue" },
      { prodID: "ice379", name: "Wicked Black" },
      { prodID: "ice380", name: "Wine" },
      { prodID: "ice49", name: "Caddis Green" },
      { prodID: "ice50", name: "Callibaetis" },
      { prodID: "ice54", name: "Chartreuse" },
      { prodID: "ice59", name: "Chocolate Brown" },
      { prodID: "ice61", name: "UV Cinnamon" },
      { prodID: "ice63", name: "Claret" },
      { prodID: "ice67", name: "Copper" },
      { prodID: "ice95", name: "UV Dark Olive" },
    ],
  },
  {
    divClass: "uvHareline",
    colors: [
      { prodID: "ice106", name: "UV Dun" },
      { prodID: "ice11", name: "UV Black" },
      { prodID: "ice165", name: "UV Gray" },
      { prodID: "ice187", name: "UV Hot Orange" },
      { prodID: "ice200", name: "UV Lavender" },
      { prodID: "ice210", name: "UV Light Gray" },
      { prodID: "ice212", name: "UV Lt. Olive" },
      { prodID: "ice215", name: "UV Lt. Yellow" },
      { prodID: "ice281", name: "UV Peacock Eye" },
      { prodID: "ice285", name: "UV Pearl" },
      { prodID: "ice289", name: "UV Pink" },
      { prodID: "ice310", name: "UV Red" },
      { prodID: "ice322", name: "UV Rust" },
      { prodID: "ice343", name: "UV Shrimp Pink" },
      { prodID: "ice40", name: "UV Brown" },
      { prodID: "ice369", name: "UV Tan" },
    ],
  },
  {
    divClass: "rabbitMicro",
    colors: [
      { prodID: "mic11", name: "Black" },
      { prodID: "mic35", name: "Bright Purple" },
      { prodID: "mic40", name: "Brown" },
      { prodID: "mic54", name: "Chartreuse" },
      { prodID: "mic57", name: "Chinchilla" },
      { prodID: "mic71", name: "Crawfish Orange" },
      { prodID: "mic87", name: "Dark Brown" },
      { prodID: "mic123", name: "Flesh" },
      { prodID: "mic131", name: "Fuchsia" },
      { prodID: "mic137", name: "Fl. Orange" },
      { prodID: "mic154", name: "Gold Variant" },
      { prodID: "mic176", name: "Grizzly" },
      { prodID: "mic187", name: "Hot Orange" },
      { prodID: "mic188", name: "Hot Pink" },
      { prodID: "mic205", name: "Light Brown" },
      { prodID: "mic251", name: "Navy Blue" },
      { prodID: "mic263", name: "Olive" },
      { prodID: "mic270", name: "Olive Variant" },
      { prodID: "mic281", name: "Peach" },
      { prodID: "mic298", name: "Purple" },
      { prodID: "mic310", name: "Red" },
      { prodID: "mic329", name: "Salmon Pink" },
      { prodID: "mic369", name: "Tan" },
      { prodID: "mic377", name: "White" },
      { prodID: "mic383", name: "Yellow" },
    ],
  },
  {
    divClass: "rabbitCrosscut",
    colors: [
      { prodID: "crs1", name: "Black" },
      { prodID: "crs2", name: "Purple" },
      { prodID: "crs3", name: "Olive" },
      { prodID: "crs4", name: "Dark Brown" },
      { prodID: "crs5", name: "Hot Pink" },
      { prodID: "crs6", name: "Fl. Orange" },
      { prodID: "crs7", name: "Crawfish Orange" },
      { prodID: "crs8", name: "White" },
      { prodID: "crs9", name: "Light Brown" },
      { prodID: "crs10", name: "Fl. Fuschia" },
      { prodID: "crs11", name: "Chartreuse" },
      { prodID: "crs12", name: "Yellow" },
      { prodID: "crs13", name: "Charcoal Mouse Gray" },
      { prodID: "crs14", name: "Red" },
      { prodID: "crs15", name: "Gold Variant" },
      { prodID: "crs35", name: "Bright Purple" },
      { prodID: "crs38", name: "Baby Blue" },
      { prodID: "crs49", name: "Green Olive" },
      { prodID: "crs50", name: "Sculpin Olive" },
      { prodID: "crs51", name: "Lime Green" },
      { prodID: "crs57", name: "Chinchilla" },
      { prodID: "crs72", name: "Cream" },
      { prodID: "crs95", name: "Dark Olive" },
      { prodID: "crs123", name: "Flesh" },
      { prodID: "crs125", name: "Fl. Blue" },
      { prodID: "crs141", name: "Fl. Sockeye Red" },
      { prodID: "crs143", name: "Fl. Yellow Chartreuse" },
      { prodID: "crs153", name: "Gold" },
      { prodID: "crs165", name: "Gray" },
      { prodID: "crs247", name: "Grizzly Natural Hares" },
      { prodID: "crs270", name: "Olive Variant" },
      { prodID: "crs281", name: "Peach" },
      { prodID: "crs322", name: "Rust" },
      { prodID: "crs329", name: "Salmon Pink" },
      { prodID: "crs343", name: "Shrimp Pink" },
      { prodID: "crs362", name: "Steelie Blue" },
      { prodID: "crs369", name: "Tan" },
    ],
  },
  {
    divClass: "rabbitCrosscutTwo",
    colors: [
      { prodID: "xtt1", name: "Purple/Fl. Fuschia" },
      { prodID: "xtt2", name: "Purple/Hot Pink" },
      { prodID: "xtt3", name: "Black/Red" },
      { prodID: "xtt4", name: "Black/Fl. Orange" },
      { prodID: "xtt5", name: "Black/Fl. Chartreuse" },
      { prodID: "xtt6", name: "Black/Fl. Hot Pink" },
      { prodID: "xtt7", name: "Black/Olive" },
      { prodID: "xtt8", name: "Hot Orange/Yellow" },
      { prodID: "xtt9", name: "Violet/White" },
      { prodID: "xtt10", name: "Blue/White" },
      { prodID: "xtt11", name: "Hot Orange/Fl. Orange" },
      { prodID: "xtt12", name: "Bright Purple/Baby Blue" },
      { prodID: "xtt13", name: "White Tipped Hot Orange" },
      { prodID: "xtt14", name: "Chartreuse Tipped Green Chartreuse" },
      { prodID: "xtt15", name: "Yellow Tipped Hot Orange" },
      { prodID: "xtt16", name: "Hot Orange Tipped Fl. Hot Pink" },
      { prodID: "xtt17", name: "White Tipped Hot Orange" },
      { prodID: "xtt18", name: "White Tipped Hot Pink" },
      { prodID: "xtt19", name: "Alaska Flame" },
      { prodID: "xtt20", name: "Key Lime Baby Blue" },
    ],
  },
  {
    divClass: "standardTubing",
    colors: [
      { prodID: "tub11", name: "Black" },
      { prodID: "tub23", name: "Blue" },
      { prodID: "tub40", name: "Brown" },
      { prodID: "tub54", name: "Chartreuse" },
      { prodID: "tub65", name: "Clear" },
      { prodID: "tub90", name: "Dark Golden Stone" },
      { prodID: "tub165", name: "Gray" },
      { prodID: "tub209", name: "Light Golden Stone" },
      { prodID: "tub212", name: "Light Olive" },
      { prodID: "tub221", name: "Mahogany" },
      { prodID: "tub263", name: "Olive" },
      { prodID: "tub265", name: "Olive Brown" },
      { prodID: "tub271", name: "Orange" },
      { prodID: "tub281", name: "Peach" },
      { prodID: "tub282", name: "Peacock" },
      { prodID: "tub289", name: "Pink" },
      { prodID: "tub298", name: "Purple" },
      { prodID: "tub310", name: "Red" },
      { prodID: "tub321", name: "Blood Red" },
      { prodID: "tub322", name: "Rust" },
      { prodID: "tub369", name: "Tan" },
      { prodID: "tub383", name: "Yellow" },
    ],
  },
  {
    divClass: "flashabou",
    colors: [
      { prodID: "fla7961", name: "Fl. Yellow" },
      { prodID: "fla7962", name: "Chartreuse" },
      { prodID: "fla7965", name: "Pink" },
      { prodID: "fla7966", name: "Purple" },
      { prodID: "fla7969", name: "Olive" },
      { prodID: "fla7979", name: "Lavender" },
      { prodID: "fla7980", name: "Peach" },
      { prodID: "fla7981", name: "Seafoam" },
      { prodID: "fla7901", name: "Silver" },
      { prodID: "fla7905", name: "Pearl" },
      { prodID: "fla7908", name: "Electric Blue" },
      { prodID: "fla7911", name: "Red" },
      { prodID: "fla7912", name: "Black" },
      { prodID: "fla7924", name: "Rainbow" },
      { prodID: "fla7947", name: "White" },
      { prodID: "fla7950", name: "Glow in the dark White" },
      { prodID: "fla7951", name: "Glow in the dark Pink" },
      { prodID: "fla7952", name: "Glow in the dark Yellow" },
      { prodID: "fla7953", name: "Glow in the dark Blue" },
      { prodID: "fla7954", name: "Glow in the dark Orange" },
      { prodID: "fla7955", name: "Glow in the dark Green" },
    ],
  },
  {
    divClass: "ucMicro",
    colors: [
      { prodID: "ucm11", name: "Black" },
      { prodID: "ucm40", name: "Brown" },
      { prodID: "ucm48", name: "Burnt Orange" },
      { prodID: "ucm49", name: "Caddis Green" },
      { prodID: "ucm95", name: "Dark Olive" },
      { prodID: "ucm126", name: "Fl. Cerise" },
      { prodID: "ucm127", name: "Fl. Chartreuse" },
      { prodID: "ucm129", name: "Fl. Fire Orange" },
      { prodID: "ucm137", name: "Fl. Orange" },
      { prodID: "ucm138", name: "Fl. Pink" },
      { prodID: "ucm139", name: "Fl. Red" },
      { prodID: "ucm142", name: "Fl. Yellow" },
      { prodID: "ucm210", name: "Light Gray" },
      { prodID: "ucm212", name: "Light Olive" },
      { prodID: "ucm263", name: "Olive" },
      { prodID: "ucm271", name: "Orange" },
      { prodID: "ucm298", name: "Purple" },
      { prodID: "ucm310", name: "Red" },
      { prodID: "ucm369", name: "Tan" },
      { prodID: "ucm377", name: "White" },
      { prodID: "ucm380", name: "Wine" },
      { prodID: "ucm381", name: "Worm Brown" },
      { prodID: "ucm382", name: "Worm Green" },
      { prodID: "ucm383", name: "Yellow" },
    ],
  },
  {
    divClass: "ucMedium",
    colors: [
      { prodID: "uc11", name: "Black" },
      { prodID: "uc40", name: "Brown" },
      { prodID: "uc48", name: "Burnt Orange" },
      { prodID: "uc49", name: "Caddis Green" },
      { prodID: "uc95", name: "Dark Olive" },
      { prodID: "uc126", name: "Fl. Cerise" },
      { prodID: "uc127", name: "Fl. Chartreuse" },
      { prodID: "uc129", name: "Fl. Fire Orange" },
      { prodID: "uc137", name: "Fl. Orange" },
      { prodID: "uc138", name: "Fl. Pink" },
      { prodID: "uc139", name: "Fl. Red" },
      { prodID: "uc142", name: "Fl. Yellow" },
      { prodID: "uc212", name: "Light Olive" },
      { prodID: "uc263", name: "Olive" },
      { prodID: "uc271", name: "Orange" },
      { prodID: "uc298", name: "Purple" },
      { prodID: "uc310", name: "Red" },
      { prodID: "uc369", name: "Tan" },
      { prodID: "uc377", name: "White" },
      { prodID: "uc380", name: "Wine" },
      { prodID: "uc381", name: "Worm Brown" },
      { prodID: "uc382", name: "Worm Green" },
      { prodID: "uc383", name: "Yellow" },
    ],
  },
  {
    divClass: "siliLegs",
    colors: [
      { prodID: "sf012", name: "Orange" },
      { prodID: "sf510", name: "Fl. Pink" },
      { prodID: "sf120", name: "Amber" },
      { prodID: "sf153", name: "Pumpkin" },
      { prodID: "sf254", name: "Pearl Silver" },
      { prodID: "sf144", name: "Sand" },
      { prodID: "sf509", name: "Fl. Chartreuse" },
      { prodID: "sf088", name: "Olive" },
      { prodID: "sf047", name: "Brown" },
      { prodID: "sf009", name: "Hopper Yellow" },
      { prodID: "sf000", name: "White" },
    ],
  },
];

const UVColors = [
  { prodID: "ice106", name: "UV Dun" },
  { prodID: "ice11", name: "UV Black" },
  { prodID: "ice165", name: "UV Gray" },
  { prodID: "ice187", name: "UV Hot Orange" },
  { prodID: "ice200", name: "UV Lavender" },
  { prodID: "ice210", name: "UV Light Gray" },
  { prodID: "ice212", name: "UV Lt. Olive" },
  { prodID: "ice215", name: "UV Lt. Yellow" },
  { prodID: "ice281", name: "UV Peacock Eye" },
  { prodID: "ice285", name: "UV Pearl" },
  { prodID: "ice289", name: "UV Pink" },
  { prodID: "ice310", name: "UV Red" },
  { prodID: "ice322", name: "UV Rust" },
  { prodID: "ice343", name: "UV Shrimp Pink" },
  { prodID: "ice40", name: "UV Brown" },
  { prodID: "ice369", name: "UV Tan" },
];
const regularColors = [
  { prodID: "ice109", name: "Electric Purple" },
  { prodID: "ice110", name: "Emerald Green" },
  { prodID: "ice133", name: "Flo. Hot Pink" },
  { prodID: "ice134", name: "Fl. Lime Green" },
  { prodID: "ice142", name: "Fl. Yellow" },
  { prodID: "ice153", name: "Gold 			" },
  { prodID: "ice157", name: "Golden Brown" },
  { prodID: "ice177", name: "Gunmetal Grey" },
  { prodID: "ice189", name: "Hot Yellow" },
  { prodID: "ice199", name: "Kingfisher Blue" },
  { prodID: "ice23", name: "Blue Steelie" },
  { prodID: "ice232", name: "Midnight Fire" },
  { prodID: "ice233", name: "Midnight Haze" },
  { prodID: "ice234", name: "Minnow Belly" },
  { prodID: "ice235", name: "Minnow Pearl Back" },
  { prodID: "ice263", name: "Olive" },
  { prodID: "ice265", name: "Olive Brown" },
  { prodID: "ice271", name: "Orange" },
  { prodID: "ice282", name: "Peacock" },
  { prodID: "ice283", name: "Peacock Black" },
  { prodID: "ice284", name: "Pearl" },
  { prodID: "ice286", name: "Pearl Red Hue" },
  { prodID: "ice288", name: "Pheasant Tail" },
  { prodID: "ice298", name: "Purple" },
  { prodID: "ice300", name: "Red" },
  { prodID: "ice323", name: "Rusty Brown" },
  { prodID: "ice340", name: "Flo. Shell Pink" },
  { prodID: "ice344", name: "Silver Holographic" },
  { prodID: "ice345", name: "Silver " },
  { prodID: "ice350", name: "Silver Grey" },
  { prodID: "ice359", name: "Smolt Blue" },
  { prodID: "ice379", name: "Wicked Black" },
  { prodID: "ice380", name: "Wine" },
  { prodID: "ice49", name: "Caddis Green" },
  { prodID: "ice50", name: "Callibaetis" },
  { prodID: "ice54", name: "Chartreuse" },
  { prodID: "ice59", name: "Chocolate Brown" },
  { prodID: "ice61", name: "UV Cinnamon" },
  { prodID: "ice63", name: "Claret" },
  { prodID: "ice67", name: "Copper" },
  { prodID: "ice95", name: "UV Dark Olive" },
];
const ucMicro = [
  { prodID: "ucm11", name: "Black" },
  { prodID: "ucm40", name: "Brown" },
  { prodID: "ucm48", name: "Burnt Orange" },
  { prodID: "ucm49", name: "Caddis Green" },
  { prodID: "ucm95", name: "Dark Olive" },
  { prodID: "ucm126", name: "Fl. Cerise" },
  { prodID: "ucm127", name: "Fl. Chartreuse" },
  { prodID: "ucm129", name: "Fl. Fire Orange" },
  { prodID: "ucm137", name: "Fl. Orange" },
  { prodID: "ucm138", name: "Fl. Pink" },
  { prodID: "ucm139", name: "Fl. Red" },
  { prodID: "ucm142", name: "Fl. Yellow" },
  { prodID: "ucm210", name: "Light Gray" },
  { prodID: "ucm212", name: "Light Olive" },
  { prodID: "ucm263", name: "Olive" },
  { prodID: "ucm271", name: "Orange" },
  { prodID: "ucm298", name: "Purple" },
  { prodID: "ucm310", name: "Red" },
  { prodID: "ucm369", name: "Tan" },
  { prodID: "ucm377", name: "White" },
  { prodID: "ucm380", name: "Wine" },
  { prodID: "ucm381", name: "Worm Brown" },
  { prodID: "ucm382", name: "Worm Green" },
  { prodID: "ucm383", name: "Yellow" },
];

$(document).ready(() => {
  for (index in products) {
    // console.log(prod);
    $("." + products[index].divClass + " .mainImage")
      .attr(
        "src",
        "./images/fly_Tying/hareline/" +
          products[index].colors[0].prodID +
          ".jpg"
      )
      .data("productid", products[index].colors[0].prodID)
      .attr("alt", products[index].colors[0].name);
    $("." + products[index].divClass + " .product-details #os0").val(
      products[index].colors[0].name
    );
    $("." + products[index].divClass + " .product-details #item_number").val(
      products[index].colors[0].prodID
    );
    $("." + products[index].divClass + "#product-name").html(
      products[index].colors[0].name
    );
    for (colorIndex in products[index].colors) {
      $("." + products[index].divClass).append(
        '<img src="./images/fly_Tying/hareline/' +
          products[index].colors[colorIndex].prodID +
          '.jpg" alt="' +
          products[index].colors[colorIndex].name +
          '" data-productid="' +
          products[index].colors[colorIndex].prodID +
          '" class="product-image">'
      );
    }
  }
  //   $(".regularHareline .mainImage")
  //     .attr(
  //       "src",
  //       "./images/fly_Tying/hareline/" + regularColors[0].prodID + ".jpg"
  //     )
  //     .data("productid", regularColors[0].prodID)
  //     .attr("alt", regularColors[0].name);
  //   $(".regularHareline .product-details #os0").val(regularColors[0].name);
  //   $(".regularHareline .product-details #item_number").val(
  //     regularColors[0].prodID
  //   );
  //   $(".regularHareline #product-name").html(regularColors[0].name);
  //   for (index in regularColors) {
  //     $(".regularHareline").append(
  //       '<img src="./images/fly_Tying/hareline/' +
  //         regularColors[index].prodID +
  //         '.jpg" alt="' +
  //         regularColors[index].name +
  //         '" data-productid="' +
  //         regularColors[index].prodID +
  //         '">'
  //     );
  //   }
  //   $(".uvHareline .mainImage")
  //     .attr("src", "./images/fly_Tying/hareline/" + UVColors[0].prodID + ".jpg")
  //     .data("productid", UVColors[0].prodID)
  //     .attr("alt", UVColors[0].name);
  //   $(".uvHareline .product-details #os0").val(UVColors[0].name);
  //   $(".uvHareline .product-details #item_number").val(UVColors[0].prodID);
  //   $(".uvHareline #product-name").html(UVColors[0].name);
  //   for (index in UVColors) {
  //     $(".uvHareline").append(
  //       '<img src="./images/fly_Tying/hareline/' +
  //         UVColors[index].prodID +
  //         '.jpg" alt="' +
  //         UVColors[index].name +
  //         '" data-productid="' +
  //         UVColors[index].prodID +
  //         '">'
  //     );
  //   }
  //   $(".ucMicro .mainImage")
  //     .attr("src", "./images/fly_Tying/hareline/" + ucMicro[0].prodID + ".jpg")
  //     .data("productid", ucMicro[0].prodID)
  //     .attr("alt", ucMicro[0].name);
  //   $(".ucMicro .product-details #os0").val(ucMicro[0].name);
  //   $(".ucMicro .product-details #item_number").val(ucMicro[0].prodID);
  //   $(".ucMicro #product-name").html(ucMicro[0].name);
  //   for (index in ucMicro) {
  //     $(".ucMicro").append(
  //       '<img src="./images/fly_Tying/hareline/' +
  //         ucMicro[index].prodID +
  //         '.jpg" alt="' +
  //         ucMicro[index].name +
  //         '" data-productid="' +
  //         ucMicro[index].prodID +
  //         '">'
  //     );
  //   }
  $(".regularHareline img").click(function () {
    $(".regularHareline .mainImage").attr("src", $(this).attr("src"));
    $(".regularHareline #product-name").html($(this).attr("alt"));
    $(".regularHareline .product-details #os0").val($(this).attr("alt"));
    $(".regularHareline.product-details #item_number").val(
      $(this).data("productid")
    );
  });
  $(".uvHareline img").click(function () {
    $(".uvHareline .mainImage").attr("src", $(this).attr("src"));
    $(".uvHareline #product-name").html($(this).attr("alt"));
    $(".uvHareline .product-details #os0").val($(this).attr("alt"));
    $(".uvHareline .product-details #item_number").val(
      $(this).data("productid")
    );
  });
  $(".ucMicro img").click(function () {
    $(".ucMicro .mainImage").attr("src", $(this).attr("src"));
    $(".ucMicro #product-name").html($(this).attr("alt"));
    $(".ucMicro .product-details #os0").val($(this).attr("alt"));
    $(".ucMicro .product-details #item_number").val($(this).data("productid"));
  });
  $(".ucMedium img").click(function () {
    $(".ucMedium .mainImage").attr("src", $(this).attr("src"));
    $(".ucMedium #product-name").html($(this).attr("alt"));
    $(".ucMedium .product-details #os0").val($(this).attr("alt"));
    $(".ucMedium .product-details #item_number").val($(this).data("productid"));
  });
  $(".rabbitMicro img").click(function () {
    $(".rabbitMicro .mainImage").attr("src", $(this).attr("src"));
    $(".rabbitMicro #product-name").html($(this).attr("alt"));
    $(".rabbitMicro .product-details #os0").val($(this).attr("alt"));
    $(".rabbitMicro .product-details #item_number").val(
      $(this).data("productid")
    );
  });

  $(".rabbitCrosscut img").click(function () {
    $(".rabbitCrosscut .mainImage").attr("src", $(this).attr("src"));
    $(".rabbitCrosscut #product-name").html($(this).attr("alt"));
    $(".rabbitCrosscut .product-details #os0").val($(this).attr("alt"));
    $(".rabbitCrosscut .product-details #item_number").val(
      $(this).data("productid")
    );
  });

  $(".rabbitCrosscutTwo img").click(function () {
    $(".rabbitCrosscutTwo .mainImage").attr("src", $(this).attr("src"));
    $(".rabbitCrosscutTwo #product-name").html($(this).attr("alt"));
    $(".rabbitCrosscutTwo .product-details #os0").val($(this).attr("alt"));
    $(".rabbitCrosscutTwo .product-details #item_number").val(
      $(this).data("productid")
    );
  });
  $(".flashabou img").click(function () {
    $(".flashabou .mainImage").attr("src", $(this).attr("src"));
    $(".flashabou #product-name").html($(this).attr("alt"));
    $(".flashabou .product-details #os0").val($(this).attr("alt"));
    $(".flashabou .product-details #item_number").val(
      $(this).data("productid")
    );
  });
  $(".siliLegs img").click(function () {
    $(".siliLegs .mainImage").attr("src", $(this).attr("src"));
    $(".siliLegs #product-name").html($(this).attr("alt"));
    $(".siliLegs .product-details #os0").val($(this).attr("alt"));
    $(".siliLegs .product-details #item_number").val($(this).data("productid"));
  });
  $(".standardTubing img").click(function () {
    $(".standardTubing .mainImage").attr("src", $(this).attr("src"));
    $(".standardTubing #product-name").html($(this).attr("alt"));
    $(".standardTubing .product-details #os0").val($(this).attr("alt"));
    $(".standardTubing .product-details #item_number").val(
      $(this).data("productid")
    );
  });
});
