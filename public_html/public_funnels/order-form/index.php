<?php
				
				$ob->setInfo('mysqli',$mysqli);
		        $ob->setInfo('dbpref',$dbpref);
				$ob->setInfo('load',$ob);
				$GLOBALS['ob']=$ob;
				$dir=__DIR__;
				$funnel=$ob->loadFunnel();
                $funnel->userIndexContent(2,$dir,'f4z121688399890');
				?>