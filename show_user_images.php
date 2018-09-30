<?php require_once("session.php");
require_once("dbConnection.php");
require_once("functions.php");
require_once("dbFunctions.php");
require_once("systemConstants.php");

find_selected_user();
find_logged_in_user();
					
if (!$selectedUser) {
	redirect_to("index.php");  
} 
$userId=(int)$selectedUser["id"];	
$loggedId= (int) $loggedInUser["id"];

include("navigation.php"); ?>

<section>	
	<div  id="userInfo">
		<h1><?php echo htmlentities($selectedUser["name"])?></h1> 	 
		<p><?php echo htmlentities($selectedUser["description"])?></p>
	</div>
					
	<!-- Up and down arrow-->
	<img id="upArrow" src="/assets/down.png"  alt="Go up" onclick="scrollUp()">
	<img id="downArrow" src="/assets/down.png"  alt="Go down" onclick="scrollDown()">
	
	<!--Image gallery-->		
	<div id="gallery">
	<?php 
		$query = "SELECT * ";
		$query.= "FROM images ";
		$query.= "WHERE user_id = {$userId} ";
		$query.= "ORDER BY  timestamp DESC;";
		$result = mysqli_query($connection,$query);
		
		//Number of images
		$rowcount=mysqli_num_rows($result);
		
		check_result($result,"show_user_images.php:Could not retrieve image entries for user: ".$selectedUser["name"]);
		//Image counter
		$count=0;
		$urlArray = array();
		while($row = mysqli_fetch_assoc($result)) {
			$imageId=(int) $row["image_id"];
			$safeFileName=htmlentities($row["safe_file_name"]);	
		 	$imageUrl=PICTURE_ROOT.htmlentities($selectedUser["user_dir"])."/".$safeFileName;
		 	$count++;?>	
		 	<div class="pictureFrame">
		  	 	<span class="counter"><?php echo $count." / ".$rowcount."/".$imageId?></span>
				<img src="<?php echo $imageUrl?>" alt="Some image" onclick="openModal(this)">
				<div class="caption">
					<?php //If this is an admin or this is the logged in user's account	
						if($loggedInUser["admin"]||($loggedId==$userId)){?>
						<!--Image delete x'ies-->
						<a href="javascript:void(0)" title="Delete" onclick="showDeletePicture(this)">&times;</a>
						<?php } ?>
						<p><?php echo htmlentities($row["description"])?></p>
				</div>
			</div> <!-- pictureFrame -->
		<?php } //while ?> 	
			
			<!-- Delete picture popup-->
			<div id="deletePictureAlert">
				<p>Do you really want to delete this picture?</p>
				<div id="OkCancel">
					<a id="okbutton" class="button" href="" onclick="document.getElementById('deletePictureAlert').style.display = 'none'">OK</a>
					<button class="cancelButton" type="button" onclick="document.getElementById('deletePictureAlert').style.display = 'none'">Cancel</button>
				</div>
			</div>

			<!-- Modal container-->
			<div id="modalDiv">
			  	<span class="close" onclick="closeModal()">&times;</span>
				 <img id="modalImage" alt="Missing image">
				 <p id="modalCaption"></p>
				 <span id="modalCounter"></span>
			    <!-- Next and previous buttons -->
  					<button class="button prev" onclick="showImage(-1)">&#10094;</button>
  					<button class="button next" onclick="showImage(1)">&#10095;</button>
			</div>				
			<?php mysqli_free_result($result);?>
	</div>	
</section>	
	
<?php include("footer.php"); ?>

<script>

function showDeletePicture(deleteLink) {
	//The imageId is the 3rd substring in counter of the pictureFrame
	var imageId=deleteLink.parentElement.parentElement.getElementsByClassName("counter")[0].innerHTML.split("/")[2];
	var button=document.getElementById("okbutton");
	button.href="delete_image.php?imageId="+imageId;
	document.getElementById('deletePictureAlert').style.display = 'block';
}

function scrollUp() {
	var initialOffset=window.pageYOffset;
	//Track scroll distance with these offsets
	var oldOffset,newOffset=0; 
	//Amount in px that function will scroll at a time - approximate height of one pictureFrame
	var scrollDistance = 500; 
	var id = setInterval(moveUp, 10);
  	function moveUp() {
  		oldOffset=window.pageYOffset;
		window.scrollBy(0,-10);
		newOffset=window.pageYOffset;
		//When you reach the top of the page or the scrollDistance amount - stop the bus
   	if (newOffset<(initialOffset-scrollDistance)||(oldOffset==newOffset)) {
     		clearInterval(id);
   	} 
  }
} 
 
function scrollDown() {
	var initialOffset=window.pageYOffset;
	//Track scroll distance with these offsets
	var oldOffset,newOffset=0; 
	//Amount in px that function will scroll at a time - approximate height of one pictureFrame
	var scrollDistance = 500; 
	var id = setInterval(moveDown, 10);
  	function moveDown() {
  		oldOffset=window.pageYOffset;
		window.scrollBy(0,10);
		newOffset=window.pageYOffset;
		//When you reach the bottom of the page or the scrollDistance amount - stop the bus
   	if (newOffset>(initialOffset+scrollDistance)||(oldOffset==newOffset)) {
     		clearInterval(id);
   	} 
  }
}


//Keep track of image index in modal
var imageIndex; 
var totalCount;

/*When user clicks on a pictureFrame, show modal*/
function openModal(image) {
	//Only open modal at screen width greater than 606px - disabled for small screens 
	if (screen.width>606) {
		document.getElementById("modalDiv").style.display="block";
		document.getElementById("modalImage").src = image.src;		
		document.getElementById("modalCaption").innerHTML=image.nextElementSibling.querySelector("p").innerHTML;
		modalIndexArray = image.previousElementSibling.innerHTML.split("/");
		//Why does imageIndex end up being an array? It should be an integer by default!
		imageIndex = Number(modalIndexArray[0]);
		totalCount =Number(modalIndexArray[1]);
		document.getElementById("modalCounter").innerHTML=imageIndex+"/"+totalCount;
	}
}

/*When user clicks X on modal image*/
function closeModal() {
	document.getElementById("modalDiv").style.display="none";
}

/* Show prev/next image in modal */
function showImage(n) {
	imageIndex += n;
	if (imageIndex <= 0) { 
		imageIndex=totalCount;
	}	
	if (imageIndex > totalCount) {
		imageIndex=1;
	}
	var frameList = document.getElementsByClassName("pictureFrame");
	var newImage =  frameList[imageIndex-1].querySelector("img");
	document.getElementById("modalImage").src = newImage.src;
	document.getElementById("modalCaption").innerHTML=newImage.nextElementSibling.querySelector("p").innerHTML;
	document.getElementById("modalCounter").innerHTML=imageIndex+"/"+totalCount;
}
 
</script>
