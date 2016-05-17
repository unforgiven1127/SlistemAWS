<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/component/display/resources/class/template/template.tpl.class.php5');

class CCandidate_sl3 extends CTemplate
{
  protected $coDisplay = null;

  public function __construct(&$poTplManager, $psUid, $pasParams, $pnTemplateNumber)
  {
    $this->csTplType = 'bloc';
    $this->casTplToLoad = array();
    $this->casTplToProvide = array();

    $this->coDisplay = CDependency::getCpHtml();

    $oPage = CDependency::getCpPage();
    $oPage->addCssFile('/component/sl_candidate/resources/css/sl_candidate.css');

    parent::__construct($poTplManager, $psUid, $pasParams, $pnTemplateNumber);
  }

  public function getTemplateType()
  {
    return $this->csTplType;
  }

  public function getRequiredFeatures()
  {
    return array('to_load' => $this->casTplToLoad, 'to_provide' => $this->casTplToProvide);
  }


  public function getDisplay($pasCandidateData, $pasDisplayParams)
  {
    $oCandidate = CDependency::getComponentByName('sl_candidate');

    $sExtraStatus = '';
    $asStatus = $oCandidate->getVars()->getCandidateStatusList(true);
    $asGrade = $oCandidate->getVars()->getCandidateGradeList();
    $slPositionLinkResult = $oCandidate->getVars()->getSlPositionLinkCandidate($pasCandidateData['candidatefk']);


    /*$asLocation = $oCandidate->getVars()->getLocationList();
    $asNationality = $oCandidate->getVars()->getNationalityList();
    $asLanguage = $oCandidate->getVars()->getlanguageList();*/
    if($pasCandidateData['sex'] == 1)
       $sGenderClass = 'man';
     else
       $sGenderClass = 'woman';

    $pasCandidateData['sl_candidatepk'] = (int)$pasCandidateData['sl_candidatepk'];


    $oRight = CDependency::getComponentByName('right');
    $oLogin = CDependency::getCpLogin();
    $nCurrentUser = $oLogin->getUserPk();

    $oPage = CDependency::getCpPage();
    $oPage->addCssFile('/component/sl_candidate/resources/css/slistem3.css');
    //$asItem = array('cp_uid' => '555-001', 'cp_action' => CONST_ACTION_VIEW, 'cp_type' => CONST_CANDIDATE_TYPE_CANDI, 'cp_pk' => $pasCandidateData['sl_candidatepk']);


    //if user is here with a candidate that has a redirect ==> admin (we still make sure)
    $bAdmin = $oRight->canAccess('555-001', 'adm_dba', CONST_CANDIDATE_TYPE_CANDI);
    if(!empty($pasCandidateData['_sys_redirect']) || !empty($pasCandidateData['_sys_status']))
    {
      $sAdminClass = 'view_admin';
      $sExtraStatus = '[Merged / deleted] &nbsp;&nbsp;&nbsp;';
    }
    else
      $sAdminClass = '';

    //start a data section
    $sHTML = $this->coDisplay->getBlocStart('', array('class' => 'candiTopSection '.$sAdminClass));

      

    return $sHTML;
  }

  private function _getShortenText($psString, $pnLength = 20)
  {
    if(!assert('is_key($pnLength) && $pnLength > 3'))
      return $psString;

    if(strlen($psString) <= $pnLength)
      return $psString;

    $oPage = CDependency::getCpPage();

    $sJavascript = ' setTimeout(\'setViewTooltip(); \', 750); ';
    $oPage->addCustomJs($sJavascript);

    $sLink = substr($psString, 0, ($pnLength-3)).' ';
    $sLink.= '<a href="javascript:;" onclick="$(\'#myTooltip\').tooltip(\'open\');" title="'.str_replace('"', '\'', $psString).'" class="openTooltip">
      <span class="candi_text_shorten">&nbsp;&nbsp;&nbsp;&nbsp;</span></a>';

    return $sLink;
  }


  private function _getStatusBar($pasCandidateData)
  {
    $nPlay = $pasCandidateData['_in_play'];
    $sMeeting = substr($pasCandidateData['date_meeting'], 0, 10);

    $sHTML = $this->coDisplay->getBlocStart('', array('class' => 'candi_status_bar'));

      if(!empty($sMeeting))
      {
        $oPage = CDependency::getCpPage();
        $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_MEETING, $pasCandidateData['sl_candidatepk']);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width= 800; oConf.height = 550; goPopup.setLayerFromAjax(oConf, \''.$sURL.'\');';

        if($sMeeting == date('Y-m-d'))
          $sHTML.= '<div class="candi_status_icon meeting_set" title="Next meeting is set for today !"><a href="javascript:;" onclick="'.$sJavascript.'" style="color: #FFBF51;">'.$sMeeting.'</a></div>';
        else
          $sHTML.= '<div class="candi_status_icon meeting_set" title="Next meeting is set for the '.$sMeeting.'"><a href="javascript:;" onclick="'.$sJavascript.'" >'.$sMeeting.'</a></div>';
      }

      if($nPlay > 0)
      {
        $sHTML.= '<div class="candi_status_icon in_play" title="Candidate is active for '.$nPlay.' position(s)"><a href="javascript:;" onclick="$(\'#tabLink8\').click();" >'.$nPlay.' position(s) </a></div>';
      }
      elseif($nPlay < 0)
      {
        $sHTML.= '<div class="candi_status_icon low_priority was_in_play" title="Candidate was in play in the past"><a href="javascript:;" onclick="$(\'#tabLink8\').click();" >'.$nPlay.' | '.abs($nPlay).' position(s) played</a></div>';
      }
      else
      {
        //$sHTML.= '<span class="not_in_play" title="Candidate is not in play">&nbsp;</span>';
        $sHTML.= '<div class="candi_status_icon inactive not_in_play" title="Candidate has never been in play"></div>';
      }

      $nRating = (int)$pasCandidateData['profile_rating'];
      if(empty($nRating))
        $nRating = (int)$pasCandidateData['rating'];

      if($nRating > 85)
        $sHTML.= '<div class="candi_status_icon icon_quality featured high_quality">Q: '.$nRating.'%</span>';
      elseif($nRating > 70)
        $sHTML.= '<div class="candi_status_icon icon_quality">Q: '.$nRating.'%</span>';
      else
        $sHTML.= '<div class="candi_status_icon icon_quality low_priority">Q: '.$nRating.'%</span>';

    $sHTML.= $this->coDisplay->getBlocEnd();

    return $sHTML;
  }

}