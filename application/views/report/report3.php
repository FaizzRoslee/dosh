<div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    Laporan CIMS 
                                </div>
                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-md-2 px-2">
                                        	 <fieldset class="form-group">
                                            <label>Dari </label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i>
                                                </span>
                                                <input id="dari" type="date" class="">
                                            </div>
                                        </fieldset>
					</div>
					<div class="col-md-2 px-2">
                                                 <fieldset class="form-group">
                                            <label>Hingga </label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i>
                                                </span>
                                                <input id="hingga" type="date" class="" >
                                            </div>
                                        </fieldset>
                                        </div>
					<div class="col-md-2 px-2">
                                                 <fieldset class="form-group">
                                            <label>Negeri </label>
                                            <div class="input-group">
						<select class="form-control" id="negeri">
                                                <?php foreach ($negeri as $n) { ?>
						<option value="<?=$n->State_ID?>"><?=$n->State_Name?></option>
						<?php } ?>
                                            </select>

                                            </div>
                                        </fieldset>
                                        </div>
					<div class="col-md-2 px-2">
                                        	  <fieldset class="form-group">
						<label> </label>
						<div class="input-group">
							
						<button class="btn btn-primary" id="jana">Jana Laporan</button>
						</div>
						</fieldset>
					</div>

                                    </div>
				    <div class="row">
					<div id="laporan" class="col-md-10 px-2">

					</div>
				    </div>
                                </div>
                            </div>
                            <!--/.card-->
                        </div>
                    </div>
<script>

	$("#jana").click(function(){

		 $("#laporan").html("menjana laporan cims ....");
		 $("#laporan").load('/dosh/paparlaporan3',{dari:$("#dari").val(),hingga:$("#hingga").val(),negeri:$("#negeri").val()});

	});
	
	
</script>

