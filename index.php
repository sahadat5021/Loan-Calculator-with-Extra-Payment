<?php 
	// if(isset($_POST['reset'])){
		// header("Location: {$_SERVER['PHP_SELF']}");
		// exit;
	// }
	if(isset($_POST['submit'])){
		$loan_amount = trim($_POST['loan_amount']);
		
		$anu_int_rt = trim($_POST['anu_int_rt']);
		$ln_prd_years = trim($_POST['ln_prd_years']);
		$pmnt_p_year = trim($_POST['pmnt_p_year']);
		$start_date = trim($_POST['start_date']);
		$extra_pmnt = empty($_POST['extra_pmnt'])? 0 : trim($_POST['extra_pmnt']);
		$submit = true;
	}else{
		$loan_amount = '';
		
		$anu_int_rt = '';
		$ln_prd_years = '';
		$pmnt_p_year = '';
		$start_date = '';
		$extra_pmnt = '';
		$submit = false;
	}
?>
<html>
	<head>
		<title>Autometic Calculation</title>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		<style>
			table { margin: 1em; border-collapse: collapse; }
			td, th { padding: .1em; border: 1px #ccc solid; }
			thead { background: #D6EEEE; } 
			input{border: 1px solid #D6EEEE; }
			
			tr.odd { background: #F5FBFB; } 
			tr.even { background: #fgg; } 
		</style>
	</head>
	<body>
		<form name="entry_form" id="entry_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<table width="40%" align="center">
				<thead>
				<tr>
					<th colspan="2" align="center">Enter Values</th>
				</tr>
				</thead>
				<tbody>
				<tr class="odd">
					<td width="50%">Loan amount :</td>
					<td width="50%"><input type="text" name="loan_amount" size="30" id="loan_amount" value="<?php echo $loan_amount; ?>" /></td>
				</tr>
				<tr class="even">
					<td>Annual interest rate :</td>
					<td><input type="text" name="anu_int_rt" id="anu_int_rt" value="<?php echo $anu_int_rt; ?>" size="30" /></td>
				</tr>
				<tr class="odd">
					<td>Loan period in years :</td>
					<td><input type="text" name="ln_prd_years" id="ln_prd_years" value="<?php echo $ln_prd_years; ?>" size="30" /></td>
				</tr>
				<tr class="even">
					<td>Number of payments per year :</td>
					<td><input type="text" name="pmnt_p_year" id="pmnt_p_year" value="<?php echo $pmnt_p_year; ?>" size="30" /></td>
				</tr>
				<tr class="odd">
					<td>Start date of loan :</td>
					<td><input type="text" name="start_date" id="start_date" value="<?php echo $start_date; ?>" size="30" readonly="readonly"/></td>
				</tr>
				<tr class="even">
					<td>Optional extra payments :</td>
					<td><input type="text" name="extra_pmnt" id="extra_pmnt" value="<?php echo $extra_pmnt; ?>" size="30" /></td>
				</tr>
				<tr class="odd">
					<td colspan="2" align="center">
						<input type="submit" name="submit" id="submit" value="Calculate" />
						<input type="button" name="reset" id="reset" value="Reset" />
					</td>
				</tr>
				</tbody>
			</table>
		</form>
<?php 
function pmt($apr, $loanlength, $loanamount){
	if($apr==0)
		return ($loanamount/$loanlength);
    return ($apr * -$loanamount * pow((1 + $apr), $loanlength) / (1 - pow((1 + $apr), $loanlength)));
}

	if($submit === true){
		
		$Pay_Num = ($ln_prd_years*$pmnt_p_year);
		//$date = date_create_from_format('d/m/Y', $start_date);
                $date_explode = explode('/', $start_date);
                $start_date_of_lone = $date_explode[2].'-'.$date_explode[0].'-'.$date_explode[1];
		//$start_date_of_lone = false;
		/*if($date !== false){
			$start_date_of_lone = date_format($date, 'Y-m-d');
		}*/
		$ir_pr = $anu_int_rt/100;
		$ir = ($ir_pr/$pmnt_p_year);
		$np = ($ln_prd_years*$pmnt_p_year);
		$p_amount = pmt($ir, $np, $loan_amount);
		//$total_payment = ($p_amount+$extra_pmnt);
		$beg_balance = trim($_POST['loan_amount']);
		
		$cumulative_interest = 0;
		$early_payment = 0;
		$total_actual_payment = 0;
?>
		<table width="70%" align="left" id="details_table">
			<thead>
			<tr>
				<th>Pmt. No.</th>
				<th>Payment Date</th>
				<th>Beginning Balance</th>
				<th>Scheduled Payment</th>
				<th>Extra Payment</th>
				<th>Total Payment</th>
				<th>Principal</th>
				<th>Interest</th>
				<th>Ending Balance</th>
				<th>Cumulative Interest</th>
			</tr>
			</thead>
			<tbody>
<?php		
		
		for($i = 1; $i<=$Pay_Num; $i++){
			$month = date("n", strtotime($start_date_of_lone));
			
			$total_month = (int) $month+($i*12/$pmnt_p_year);
			$year  = (int)($total_month / 12);
			$month = ($total_month % 12 );
			if($month == '0'){
				 $month = '12';
			}
			$day = date("d", strtotime($start_date_of_lone));
			$main_year = date("Y", strtotime($start_date_of_lone));
			
			$year += $main_year;
			$final_date = $month.'/'.$day.'/'.$year;
			
			if($beg_balance<=0){
				
				break;
			}
			
			if(($p_amount+$extra_pmnt)<$beg_balance){
				 $extra_pmnt = $extra_pmnt;
			}else{
				 $extra_pmnt = 0;
			}
			
			if(($p_amount+$extra_pmnt)<$beg_balance){
				 $total_payment = $p_amount+$extra_pmnt;
			}else{
				$total_payment = $beg_balance;
				
			}
			
			$interest = ($beg_balance*$ir_pr/$pmnt_p_year);
			$principal = $total_payment - $interest;
			
			
			if(($p_amount+$extra_pmnt)<$beg_balance){
				$ending_balance = $beg_balance - $principal;
				
			}else{
				$ending_balance = 0;
			}
			
			$cumulative_interest += $interest;
			
			$total_actual_payment++;
			$early_payment += $extra_pmnt;
			if($i&1){
				echo '<tr class="odd">';
			}else{
				echo '<tr class="even">';
			}
?>
			
				<td><?php echo $i; ?></td>
				<td align="right"><?php echo $final_date; ?></td>
				<td align="right"><?php echo number_format($beg_balance, 2); ?></td>
				<td align="right"><?php echo number_format($p_amount, 2); ?></td>
				<td align="right"><?php echo number_format($extra_pmnt, 2); ?></td>
				<td align="right"><?php echo number_format($total_payment, 2); ?></td>
				<td align="right"><?php echo number_format($principal, 2); ?></td>
				<td align="right"><?php echo number_format($interest, 2); ?></td>
				<td align="right"><?php echo number_format($ending_balance, 2); ?></td>
				<td align="right"><?php echo number_format($cumulative_interest, 2); ?></td>
			</tr>
		
<?php
			
			$beg_balance = $ending_balance;
		}
?>
			</tbody>
		</table>
		<table width="25%" cellpadding="0" cellspacing="0" border="1" align="right" id="summery_table">
			<thead>
			<tr>
				<th colspan="2">Loan summary</th>
			</tr>
			</thead>
			<tbody>
			<tr class="odd">
				<td>Scheduled payment</td>
				<td align="right"><?php echo number_format($p_amount, 2); ?></td>
			</tr>
			<tr class="even">
				<td>Scheduled number of payments</td>
				<td align="right"><?php echo $Pay_Num; ?></td>
			</tr>
			<tr class="odd">
				<td>Actual number of payments</td>
				<td align="right"><?php echo $total_actual_payment; ?></td>
			</tr>
			<tr class="even">
				<td>Total early payments</td>
				<td align="right"><?php echo number_format($early_payment, 2); ?></td>
			</tr>
			<tr class="odd">
				<td>Total interest</td>
				<td align="right"><?php echo number_format($cumulative_interest, 2); ?></td>
			</tr>
			</tbody>
		</table>
<?php
	}
?>
		<script type="text/javascript">
			$(function() {
				$( "#start_date" ).datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: "mm/dd/yy"
				});
				
				$("#entry_form").submit(function(){
					if($("#loan_amount").val() == ''){
						alert('Please Give the Total Amount');
						return false;
					}
					if($("#anu_int_rt").val() == ''){
						alert('Please Give the Annual interest rate');
						return false;
					}
					if($("#ln_prd_years").val() == ''){
						alert('Please Give the Loan period in years');
						return false;
					}
					if($("#pmnt_p_year").val() == ''){
						alert('Please Give the Number of payments per year');
						return false;
					}
					if($("#start_date").val() == ''){
						alert('Please Select Start date of loan');
						return false;
					}
				});
				$('#reset').click(function(){
					$('#details_table').remove();
					$('#summery_table').remove();
					// $('form')[0].reset();
					$("input[type='text']").each(function(){
						this.value = '';
					});
					
				});
			});
		</script>
		
		<!--hello test comment-->
	</body>
</html>
