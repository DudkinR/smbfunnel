<?php
				
				$ob->setInfo('mysqli',$mysqli);
		        $ob->setInfo('dbpref',$dbpref);
				$ob->setInfo('load',$ob);
				$GLOBALS['ob']=$ob;
				$dir=__DIR__;
				$funnel=$ob->loadFunnel();
                $funnel->userIndexContent(1,$dir,'fnlxg1683883380');
				?>