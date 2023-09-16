<?php
				
				$ob->setInfo('mysqli',$mysqli);
		        $ob->setInfo('dbpref',$dbpref);
				$ob->setInfo('load',$ob);
				$GLOBALS['ob']=$ob;
				$dir=__DIR__;
				$funnel=$ob->loadFunnel();
                $funnel->userIndexContent(6,$dir,'6anrc1691511744');
				?>