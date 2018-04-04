<?php 

$tags_arr['mod_view'] .= <<<H
  <div class="modal fade" id="terms" tabindex="-1" role="dialog" aria-labelledby="terms-label">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">          
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title section__heading--red" id="terms-label">Terms &amp; Conditions</h4>
        </div>
        <div class="modal-body">
          {$donation_terms}
        </div>
        <div class="modal-footer">
          <button type="button" id="accept_terms" class="btn btn--red text-center" data-dismiss="modal" style="margin-top: 0;">Accept</button>
          <button type="button" class="btn text-center" data-dismiss="modal" style="margin-top: 0;">Close</button>
        </div>
      </div>
    </div>
  </div>
H;
 ?>