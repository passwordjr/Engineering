<!--====================================
=            Navigation Top            =
=====================================-->

<?php $this->load->view('includes/home-navbar'); ?>

<!--====  End of Navigation Top  ====<--></-->
<?php $this->load->view('includes/home-sidenav'); ?>
<!--ABOVE IS PERMA-->

<script>
    $(document).ready(function(){
        $('.collapsible').collapsible('open', 0);
        $('.collapsible').collapsible('open', 1);
        $('.collapsible').collapsible('open', 2);
    });
</script>

<div class="row container">
    <div class="col s12">
        <blockquote class="color-primary-green">
            <h3 class="color-black">View Topics<br><a href="<?= base_url() ?>SubjectArea/" class="waves-effect waves-dark btn red"><i class="material-icons left">arrow_back</i>Back</a></h3>
        </blockquote>
        <blockquote class="color-primary-green">
            <h6 class="color-black">These are the topics inside the subject area</h6>
        </blockquote>
    </div>
</div>
<div class="row container">
    <ul class="collapsible" data-collapsible = "expandable">
        <?php foreach($dissect as $subj_id => $sub_dissect): ?>
            <li>
                <div class="collapsible-header valign-wrapper">
                    <div class="col s8 valign-wrapper">
                        <i class="material-icons">assignment</i>
                        <?php echo $sub_dissect["subj_name"] ?>
                    </div>
                    <div class="col s4 right-align center">
                        <!-- LAST! - ayaw gumana ng hover -->
                        <a class='dropdown-button btn green' data-beloworigin="true" href='#' data-activates='dropdown1'>ACTIONS</a>
                        <!-- Dropdown Structure -->
                        <ul id='dropdown1' class='dropdown-content'>
                            <li><a data-id="<?=$subj_id?>" class="no-collapse waves-effect waves-dark black-text subj_edit">EDIT</a></li>
                            <li><a data-id="<?=$subj_id?>" data-name="<?=$sub_dissect['subj_name']?>" class="no-collapse waves-effect waves-dark black-text subj_remove">REMOVE</a></li>
                        </ul>
                    </div>
                </div>
                <div class="collapsible-body"><span>
                    Description: <?php echo $sub_dissect["subj_desc"] ?>
                    <br>
                    <br>
                    <br>
                    <?php if (!empty($sub_dissect["values"])): ?>
                        <table class="data-table" id="tbl-feedback" style="table-layout:auto;">
                            <thead>
                                <tr>
                                    <th>Topic</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sub_dissect["values"] as $sub_values): ?>
                                    <tr class="bg-color-white">
                                        <td><?= $sub_values["topic_list_name"] ?></td>
                                        <td><?= $sub_values["topic_list_desc"] ?></td>
                                        <td><a data-id="<?= $sub_values['topic_list_id'] ?>" class="waves-effect waves-dark btn red btn_remove">REMOVE</a></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <center style="margin-top:20vh;">
                            <h3>No data to show</h3>
                        </center>
                    <?php endif; ?>
                </span></div>
            </li>
        <?php endforeach ?>
    </ul>
</div>
<script>
    $(document).ready(function(){
        $segment = <?=$this->uri->segment(3)?>;
        $(".btn_remove").click(function () {
            $data = $(this).data('id');
            window.location.href = "<?= base_url() . "SubjectArea/remove_topic/"?>"+ $data;
        });
        $('.dropdown-button').dropdown({
            hover: true, // Activate on hover
        });
        $(".no-collapse, .dropdown-button").click(function (e) {
            e.stopPropagation();
        });
        $(".subj_edit").click(function () {
            $data = $(this).data('id');
            window.location.href = "<?= base_url() . "SubjectArea/edit_subjectarea/"?>" + $segment+"/"+$data;
        });
        $(".subj_remove").click(function(event) { 
            $id = $(this).data('id');
            $name = $(this).data('name');
            swal({
                title: "Are you sure?",
                text: "You are about to remove this Subject Area ("+$name+") to this Year Level.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "<?= base_url().'SubjectArea/delete_subject_area/' . $this->uri->segment(3)?>",
                        type: "post",
                        dataType: "json",
                        data: {
                            id: $id
                        },
                        success: function (data) {
                            swal($name+" has been deleted!", {
                                icon: "success",
                            }).then(function () {
                                window.location.reload(true);
                            });
                        },
                        error: function (data) {
                            swal("An error occured. Please try again", {
                                icon: "error",
                            }).then(function () {
                                window.location.reload(true);
                            });
                        }
                    });
                }
            }); 
        });
    });
</script>