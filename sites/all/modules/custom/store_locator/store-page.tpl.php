
 <div class="panel panel-default">
            <div class="panel-heading">
                <h4><?php print  $contents->title ;?>-<?php print  $contents->field_store_type[LANGUAGE_NONE][0]['value'] ;?></h4>
            </div>
            <div class="panel-body">
              
                <div class=" col-md-6 form-group">
					<p><label for="name">Store Manager </label> <?php print  $contents->field_store_manager[LANGUAGE_NONE][0]['value'] ;?>  </p>
                    <p><label for="name">Address </label> <?php print  $contents->field_address[LANGUAGE_NONE][0]['value'] ;?></p>
					 <p><label for="name">Telephone </label> <?php print  $contents->field_phone_number[LANGUAGE_NONE][0]['value'] ;?></p>
						<iframe src="https://maps.google.com/maps?q=<?php print  $contents->field_latitude[LANGUAGE_NONE][0]['value'] ;?>,<?php print  $contents->field_longitude[LANGUAGE_NONE][0]['value'] ;?>&hl=en&z=14&amp;output=embed" width="100%" height="400" frameborder="0" style="border:0" allowfullscreen></iframe>
			   </div>
			   
			   <div class="col-md-6">
					 <div class="table-responsive">
					 <table id="opening-times" class="table table-striped table-bordered table-hover">
					  <thead>
					  <tr>
						<th>Opening Times</th>
					  </tr>
					  </thead>
					  <tbody>
							<tr>
									<td>
											<p>Monday 9- 5:30 </p>
											<p>Teusday 9- 5:30</p>
											<p>Wednesday 9- 5:30</p>
											<p>Thursday 9- 5:30</p>
											<p>Friday 9- 5:30</p>
											<p>Saturday 9- 5:30</p>
											<p>Sunday Closed</p>
									</td>
						 </tr>
					  </tbody>
					</table> 
			 </div>
			   </div>
            </div>
        </div>
    





<div>







</div>