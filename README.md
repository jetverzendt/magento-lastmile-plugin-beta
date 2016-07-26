=========================================
Jet Verzendt met Last Mile (beta-versie!)
=========================================

Deze Magento versie is een eerste beta-versie waarin de Last Mile verwerkt is. Dit houdt in dat klanten op de checkout pagina van Magento een bezorgmoment of Parcelshop kunnen uitkiezen.
Deze eerste beta-versie werkt vooralsnog alleen op de standaard Magento uitcheckpagina (die standaard in Magento zit).

Omdat het hier om een beta-versie gaat is het goed om na installatie alles goed testen. Bij vragen of opmerkingen, geef het a.u.b. aan ons door, dan kunnen wij deze meenemen in een volgende release.


=========================================
INSTALLATIE
=========================================

1. Kopiëren van de bestanden

Kopieer de bestanden van deze plugin naar de hoofdmap binnen uw Magento webshop. De eerstvolgende keer dat uw webshop bezocht wordt zal de plugin geÃ¯nstalleerd worden. Dat houdt in dat er een aantal tabellen wordt toegevoegd aan de database. Er worden geen wijzigingen aangebracht aan bestaande tabellen.


2. Vul de Jet Verzendt authorisatie gegevens in

Om de plugin te koppelen met het Jet Verzendt-portal heeft u authorisatiegegevens nodig. Deze gegevens ontvangt u van Jet Verzendt. Deze authorisatiegegevens kunt u invoeren in het Magento configuratiescherm (System / Configuration / Jet Verzendt instellingen) onder het kopje API authorisatiegegevens. Hier kunt u tevens aangeven of u de koppeling eerst wilt testen.


3. Geef aan welke verzendmethoden naar Jet Verzendt verstuurd moeten worden
U kunt 1 of meerdere verzendmethode naar Jet Verzendt sturen.

=========================================
TIP
=========================================

Magento kent standaard een tweetal straatvelden (street1 en street2).
Jet Verzendt adviseert om het street1-veld te gebruiken voor de straatnaam en het street2-veld voor het huisnummer. 
Doet u dit niet, dan zal de Jet Verzendt plugin proberen zelf het huisnummer uit het adresveld te achterhalen, maar de kans op fouten is hierbij natuurlijk groter dan als het huisnummer al apart in een veld wordt opgegeven...
