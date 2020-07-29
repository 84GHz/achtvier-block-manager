
document.addEventListener("DOMContentLoaded", function() {
  var beremed = wp.blocks.getBlockTypes();
console.log(beremed);

  //wp.blockLibrary.registerCoreBlocks();
  var wpBlocks = wp.blocks.getBlockTypes();
  console.log(wpBlocks);

});
