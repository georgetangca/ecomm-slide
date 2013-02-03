<div id="outerContainer">
		<h2 id="welcomeMsg">Share this with your friends</h2>
		<h2 class="sts-dn" id="doneMsg">Sharing complete!</h2>
		<h2 class="sts-dn" id="moreTitle"><span class="">Share this to:</span><a class="sts-fr" id="lessLink">Back to default view</a></h2>
		<div class="sts-dn" id="chicklets"></div>
		<div id="mainBody">
			<div class="sts-dn" id="greyScreen">&nbsp;</div>
			<div id="preShareScreen">
				<form id="shareDetails" style="margin-top: 0px; margin-bottom: 0px;">
                                        <h2 class="sts-dn sharederrorMsg" id="erroCheckMsg"></h2>
					<div class="" id="emailShareDetails">
						<div id="toField"><label for="txtYourAddr">To:</label><textarea placeholder="abc1@example.com;abc2@example.com;"  wrap="soft" class="text" id="txtYourAddr" autocomplete="OFF" name="txtYourAddr"></textarea>
                                                </div>
						<div id="fromField"><label for="txtFromAddr">From:</label><input placeholder="my@example.com" type="text" name="txtFromAddr" id="txtFromAddr" class="text" autocomplete="off"></div>
					</div>
					<textarea placeholder="Write your comment here..." maxlength="2000" name="shareMessage" id="shareMessage"></textarea>
				</form>
                                <div id="articleDetails" class="">
					<a class="imgLink sts-fl"><img src="<?php echo ECOMM_POST_LINKS_IMG_DIR.'/no-image.png'; ?>" id="thumbnail"></a>
					<div id="email_body" class="sts-fr sts-oh">
					</div>
                                        <div class="sts-cb">&nbsp;</div>
				</div>
				
			</div>
			<div class="sts-dn" id="doneScreen">
                            <p id="successMsg"><span class="sharedMsg">Your message was successfully shared!</span></p>
			</div>
                	<div class="sts-dn" id="errorScreen">
                            <p id="errorMsg"><span class="sharederrorMsg">Oops! something unexpected, please try later!</span></p>
			</div>
                
		</div>
                <div id="serviceCTAs" class="sts-cb">
                    <!--	<a id="cancelLink" class="pointer ctaButton sts-fr">Cancel</a> -->
                        <a class="ctaButton pointer" id="sharebyEmailButton">Share</a>
		</div>
		
</div>