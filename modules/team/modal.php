<?php

$modal = <<<H

    <div class="modal team-modal">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 text-right">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div class="row abs-row">
                <div class="col-sm-2 text-left">
                    <a href="" class="team-nav prev">
                        <span><i class="fa fa-chevron-left"></i></span>
                        <span>Previous</span>
                        <span>Team</span>
                        <span>Member</span>
                    </a>
                </div>
                <div class="col-sm-8">
                    {$modal_items}
                </div>
                <div class="col-sm-2 text-right">
                    <a href="" class="team-nav next">
                        <span><i class="fa fa-chevron-right"></i></span>
                        <span>Next</span>
                        <span>Team</span>
                        <span>Member</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
H;


?>