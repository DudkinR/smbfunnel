<?php
	
	  $ob=new Library();
	  //auth cloud_funnels_no_conlict_index
	  $ob->setInfo('mysqli',$mysqli);
	  $ob->setInfo('dbpref',$dbpref);
	  $funnel=$ob->loadFunnel();
	  $funnel_id=17;
	  $redirectto=$funnel->goNext(__DIR__,$funnel_id,'init');
	  if($redirectto===0)
	  {
		  $ob->loadFourHunderdFour();
	  }
	  else
	  {
	  $curren_loaded_url=str_replace('//','/',$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/'.$redirectto);
	  $redirectto=getProtocol();
	   $redirectto .=$curren_loaded_url;	
      header('Location:'.$redirectto.'/');
	  }
	  ?>
	  