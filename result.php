<?php

//result.php

include('admin/srms.php');

$object = new srms();

include('header.php');

?>
	
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col col-sm-6"><h3>Result</h3></div>
				<div class="col col-sm-6 text-right">
					<a href="<?php echo $object->base_url; ?>" class="btn btn-warning btn-sm">Back</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<?php
			$download_button = '';
			if(isset($_POST["submit"]))
			{
				$data = array(
					':student_roll_no'		=>	trim($_POST["student_roll_no"])
				);
				$object->query = "
				SELECT * FROM student_srms 
				WHERE student_roll_no = :student_roll_no 
				AND student_status = 'Enable' 
				";
                $flag = '0';
				$class_id = '';
				$student_id = '';
				$result_id = '';

				$object->execute($data);

				if($object->row_count() > 0)
				{
					foreach($object->statement_result() as $student_data)
					{
						$class_id = $student_data["class_id"];
                        $student_id = $student_data["student_id"];
						$object->query = "
                        					SELECT * FROM result_srms
                        					WHERE class_id = '$class_id'
                        					AND student_id = '$student_id'
                        					AND exam_id = '".$_POST['exam_name']."'
                        					";

                        $object->execute();

                        if($object->row_count() > 0 ) {
                        $flag='1';
						echo '
						<p><b>Roll Number - </b>'.trim($_POST["student_roll_no"]).'</p>
						<p><b>Student Name - </b>'.html_entity_decode($student_data["student_name"]).'</p>
						<p><b>Level - </b>'.html_entity_decode($student_data["student_level"]).'</p>
						  <div class="row">
                                                                        							<div class="col-md-6">
                                                                        								<p style="color:#4299AC"><b>Marks Secured (BATCH 1) - '.$student_data["student_batch1_mark"].'</b></p>
                                                                        							</div>
                                                                        							<div class="col-md-6">
                                                                                                        <p style="color:#4299AC"><b>Marks Secured (BATCH 2) - '.$student_data["student_batch2_mark"].'</b></p>
                                                                        							</div>
                                                                        </div>
                        <div class="row">
                        							<div class="col-md-6">
                        								<p style="color:BlueViolet"><b>International Rank - '.$student_data["student_international_rank"].'</b></p>
                        							</div>
                        							<div class="col-md-6">
                                                        <p style="color:DarkGreen"><b>Regional Rank - '.$student_data["student_regional_rank"].'</b></p>
                        							</div>
                        </div>


						<div class="row">
							<div class="col-md-6">
								<p><b>Award - </b>'.$student_data["student_award"].'</p>
							</div>
						</div>';
                        }
                        else {
                         echo '<h4 align="center">RollNumber does not Belong to the Olympiad Exam Selected. Please select the Correct Olympiad & Enter the Corresponding Rollnumber.</h4>';
                        }
						$class_id = $student_data["class_id"];
						$student_id = $student_data["student_id"];
					}

					$object->query = "
					SELECT * FROM exam_srms 
					WHERE exam_id = '".$_POST["exam_name"]."'
					";
					$exam_result = $object->get_result();

					foreach($exam_result as $exam_data)
					{
					   if($flag > 0) {
						echo '
						<div class="row">
							<div class="col-md-6">
								<p><b>Exam - </b>'.$exam_data["exam_name"].'</p>
							</div>
							<div class="col-md-6">
								<p><b>Date & Time (UTC)- </b>'.date("Y-m-d H:i:s").'</p>
							</div>
						</div>
						';
						}
					}

					$object->query = "
					SELECT * FROM result_srms 
					WHERE class_id = '$class_id' 
					AND student_id = '$student_id' 
					AND exam_id = '".$_POST['exam_name']."'
					";

					$result_data = $object->get_result();
					foreach($result_data as $result)
					{
						if($result["result_status"] == "Enable")
						{
							$result_id = $result["result_id"];

							echo '
							<div class="table-responsive">
								<table class="table table-bordered">
									<tr>
										<th>#</th>
										<th>Subject</th>
										<th>Final Marks Awarded (Best of 2 Batches)</th>
									</tr>
							';
							$object->query = "
							SELECT subject_srms.subject_name, marks_srms.marks 
							FROM marks_srms 
							INNER JOIN subject_srms 
							ON subject_srms.subject_id = marks_srms.subject_id 
							WHERE marks_srms.result_id = '".$result["result_id"]."'
							";
							$marks_data = $object->get_result();
							$count = 0;
							$total = 0;
							foreach($marks_data as $marks)
							{
								$count++;
								echo '
									<tr>
										<td>'.$count.'</td>
										<td>'.$marks["subject_name"].'</td>
										<td>'.$marks["marks"].'</td>
									</tr>
								';
								$total += $marks["marks"];
							}
							echo '
									<tr>
										<td colspan="2" align="right"><b>Total</b></td>
										<td>'.$total.'</td>
									</tr>
									<tr>
										<td colspan="2" align="right"><b>Percentage</b></td>
										<td>'.$result["result_percentage"].'%</td>
									</tr>
								</table>
							</div>
							';
						   $download_button = '<a href="download.php?exam_id='.$_POST['exam_name'].'&student_roll_no='.$_POST["student_roll_no"].'" class="btn btn-danger"><i class="fas fa-file-pdf-o" aria-hidden="true"></i> Download Result</a>';
						}
						else
						{
							echo '<h4 align="center">Your Result has been withheld.</h4>';
						}
					}
				}
				else
				{
					echo '<h4 align="center">No Result Found</h4>';
				}
			}
			else
			{
				echo '<h4 align="center">No Result Found</h4>';
			}

			?>
			
		</div>
		<div class="card-footer text-center">
					    <?php
					      if($flag>0) {
					      echo '<p style="text-align: center;"><span style="color: #003366;"><strong><span style="color: #003366;">GC INTERNATIONAL OLYMPIADS 2022 REGISTRATION IS OPEN - Visit</span>&nbsp;<span style="text-decoration: underline; color: #0000ff;"><a style="color: #0000ff; text-decoration: underline;" href="https://www.geniuscerebrum.com/gimo/" target="blank">https://www.geniuscerebrum.com/gimo</a></span>&nbsp;<span style="color: #003366;">to Register.</span></strong></span><br /><span style="color: #003366;"><strong>USE COUPON CODE <span style="color: #ff6600;">"GC2991822H"</span> to get FLAT 60% off Exclusive for you!!!</strong></span></p><br>Note: NA denotes NOT-REGISTERED/ABSENT.<br><br> Gifts/Awards/Trophies/Medals & Prizes will be sent on or before March 15,2022 for Winners. Candidates who passed the Examination will receive their Certificate of Appreciation & Scorecard within Jan 30,2022.<br><br>';
					      }
					      ?>


			<?php echo '<input type="button" Value="Print Result" class="btn btn-danger" onclick="window.print()" />' ?>
		</div>

	</div>
	<br />

<?php

include('footer.php');

?>