# wordpress-scoutnet
## Beroenden
* phpmailer för intresseformulär via API
* Wordpress-tillägg "Advanced Custom Fields" för egna kortkoder
* Wordpress-tillägg "Wordpress-importör" för att importera inställningar (egna fält).
## Installationsanvisning
1. Installera "Advanced Custom Fields", se https://wordpress.org/plugins/advanced-custom-fields/. Behövs för att kunna skapa egna kortkoder baserade på e-postlistor.
1. Installera "Wordpress-importör". Hittas under "Verktyg--> Importera" i admingränssnittet. Behövs för att importera specifika fält som används av ovanstående plugin och för egna kortkoder. Kan också installeras via https://wordpress.org/plugins/wordpress-importer/
1. Under "Verktyg--> Importera" så kör du import av Wordpress. Ladda upp .xml filen för importen. Matcha inlägg mm gjorda av användaren "scoutest" till valfri användare.
1. Ladda ner repo här från github som .zip. https://github.com/scouternasetjanster/WP-Shortcodes-for-Scoutnet/archive/master.zip
1. I admingränssnittet under "Tillägg--> Lägg till" trycker du på "Ladda upp tillägg" och laddar upp .zip filen du precis laddade ner. Aktivera sedan tillägget.
1. Du bör nu kunna avinstallera "Wordpress-importör" om du vill.
1. Klart
