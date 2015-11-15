<?php
ini_set("display_errors","on");
error_reporting(999);

$version="test";
include_once "include/config.php";

$mailsResponsables="jerome@planningbiblio.fr";

$data=array();
$data[]=array(":nom"=>"Benoit", ":prenom"=>"Yohan", ":mail"=>"nec.ante@Crasdictumultricies.ca");
$data[]=array(":nom"=>"Masson", ":prenom"=>"Bienvenue", ":mail"=>"Mauris.vel.turpis@liberoProin.org");
$data[]=array(":nom"=>"Clement", ":prenom"=>"Candice", ":mail"=>"sapien@Nullaeu.org");
$data[]=array(":nom"=>"Perrot", ":prenom"=>"Lorenzo", ":mail"=>"dictum@actellus.co.uk");
$data[]=array(":nom"=>"Adam", ":prenom"=>"Julien", ":mail"=>"Integer.urna@Aliquamerat.edu");
$data[]=array(":nom"=>"Le goff", ":prenom"=>"Constant", ":mail"=>"sit.amet@Cumsociisnatoque.org");
$data[]=array(":nom"=>"Perrin", ":prenom"=>"Margaux", ":mail"=>"risus.at.fringilla@vitaemauris.edu");
$data[]=array(":nom"=>"Le gall", ":prenom"=>"Maëlle", ":mail"=>"tristique.aliquet.Phasellus@estarcu.co.uk");
$data[]=array(":nom"=>"Lecomte", ":prenom"=>"Anna", ":mail"=>"vulputate@Mauris.co.uk");
$data[]=array(":nom"=>"Dufour", ":prenom"=>"Julie", ":mail"=>"pharetra.sed.hendrerit@nulla.com");
$data[]=array(":nom"=>"Bouvier", ":prenom"=>"Amine", ":mail"=>"aliquet.diam.Sed@mi.com");
$data[]=array(":nom"=>"Vasseur", ":prenom"=>"Anaëlle", ":mail"=>"augue.eu@malesuadafamesac.net");
$data[]=array(":nom"=>"Giraud", ":prenom"=>"Edwige", ":mail"=>"nec@dapibusgravida.co.uk");
$data[]=array(":nom"=>"Girard", ":prenom"=>"Rose", ":mail"=>"Maecenas@lobortisaugue.com");
$data[]=array(":nom"=>"Remy", ":prenom"=>"Sara", ":mail"=>"libero.mauris.aliquam@et.edu");
$data[]=array(":nom"=>"Roger", ":prenom"=>"Noah", ":mail"=>"diam.luctus@volutpatnunc.co.uk");
$data[]=array(":nom"=>"Brun", ":prenom"=>"Lana", ":mail"=>"nascetur@InfaucibusMorbi.com");
$data[]=array(":nom"=>"Lopez", ":prenom"=>"Killian", ":mail"=>"nulla.magna.malesuada@lectusNullamsuscipit.net");
$data[]=array(":nom"=>"Gomez", ":prenom"=>"Jade", ":mail"=>"elementum.purus.accumsan@orciPhasellusdapibus.edu");
$data[]=array(":nom"=>"Renard", ":prenom"=>"Angelina", ":mail"=>"tincidunt@consectetuer.org");
$data[]=array(":nom"=>"Millet", ":prenom"=>"Emma", ":mail"=>"a.scelerisque@tortorInteger.ca");
$data[]=array(":nom"=>"Brunet", ":prenom"=>"Élise", ":mail"=>"sagittis@Nullasemper.net");
$data[]=array(":nom"=>"Gauthier", ":prenom"=>"Maïlé", ":mail"=>"nibh@nibh.org");
$data[]=array(":nom"=>"Chevalier", ":prenom"=>"Alexis", ":mail"=>"Quisque.imperdiet@ornareegestasligula.net");
$data[]=array(":nom"=>"Masson", ":prenom"=>"Lina", ":mail"=>"vel.faucibus.id@tellusPhasellus.com");
$data[]=array(":nom"=>"Duval", ":prenom"=>"Rémi", ":mail"=>"Nunc@bibendum.net");
$data[]=array(":nom"=>"Andre", ":prenom"=>"Anaël", ":mail"=>"rutrum.urna@ac.co.uk");
$data[]=array(":nom"=>"Vasseur", ":prenom"=>"Juliette", ":mail"=>"sed@Phasellusdapibusquam.org");
$data[]=array(":nom"=>"Bernard", ":prenom"=>"Nicolas", ":mail"=>"orci.sem.eget@ipsum.com");
$data[]=array(":nom"=>"Martinez", ":prenom"=>"Thomas", ":mail"=>"a.purus@Fusce.edu");
$data[]=array(":nom"=>"Prevost", ":prenom"=>"Cloé", ":mail"=>"sed.dictum@Curabiturdictum.co.uk");
$data[]=array(":nom"=>"Roger", ":prenom"=>"Gabriel", ":mail"=>"vel.convallis@elementumpurusaccumsan.ca");
$data[]=array(":nom"=>"Etienne", ":prenom"=>"Marwane", ":mail"=>"ridiculus@nequepellentesque.com");
$data[]=array(":nom"=>"Robert", ":prenom"=>"Solene", ":mail"=>"ipsum@magnanec.net");
$data[]=array(":nom"=>"Gauthier", ":prenom"=>"Sara", ":mail"=>"neque.vitae.semper@aliquamenimnec.org");
$data[]=array(":nom"=>"Morel", ":prenom"=>"Maxime", ":mail"=>"Cum@augueeu.co.uk");
$data[]=array(":nom"=>"Barre", ":prenom"=>"Gabriel", ":mail"=>"urna@nullaIntincidunt.com");
$data[]=array(":nom"=>"Paris", ":prenom"=>"Léonard", ":mail"=>"neque.sed.sem@aliquet.net");
$data[]=array(":nom"=>"Marchand", ":prenom"=>"Julien", ":mail"=>"sollicitudin.a@erat.org");
$data[]=array(":nom"=>"Andre", ":prenom"=>"Clara", ":mail"=>"consectetuer.euismod@congueelitsed.co.uk");
$data[]=array(":nom"=>"Breton", ":prenom"=>"Elsa", ":mail"=>"Mauris@porttitorvulputateposuere.org");
$data[]=array(":nom"=>"Guyot", ":prenom"=>"Jules", ":mail"=>"sagittis@aceleifend.org");
$data[]=array(":nom"=>"Meunier", ":prenom"=>"Léo", ":mail"=>"massa.non@Nuncut.edu");
$data[]=array(":nom"=>"Lopez", ":prenom"=>"Jordan", ":mail"=>"morbi.tristique@mus.ca");
$data[]=array(":nom"=>"Rodriguez", ":prenom"=>"Candice", ":mail"=>"augue.ac.ipsum@Etiamgravida.com");
$data[]=array(":nom"=>"Louis", ":prenom"=>"Chloé", ":mail"=>"Phasellus@bibendumullamcorper.com");
$data[]=array(":nom"=>"Roux", ":prenom"=>"Laura", ":mail"=>"Aenean.eget.magna@nostraper.ca");
$data[]=array(":nom"=>"Chevallier", ":prenom"=>"Marwane", ":mail"=>"risus@Mauris.com");
$data[]=array(":nom"=>"Richard", ":prenom"=>"Elsa", ":mail"=>"quis.tristique@Loremipsum.edu");
$data[]=array(":nom"=>"Michel", ":prenom"=>"Marwane", ":mail"=>"vel.mauris@vestibulumlorem.net");
$data[]=array(":nom"=>"Gauthier", ":prenom"=>"Amélie", ":mail"=>"erat.Etiam@scelerisque.edu");
$data[]=array(":nom"=>"Le roux", ":prenom"=>"Angelina", ":mail"=>"nisl.sem.consequat@Uttincidunt.org");
$data[]=array(":nom"=>"Deschamps", ":prenom"=>"Inès", ":mail"=>"Mauris.quis.turpis@placerat.ca");
$data[]=array(":nom"=>"Marechal", ":prenom"=>"Baptiste", ":mail"=>"Duis.sit@dignissimlacusAliquam.com");
$data[]=array(":nom"=>"Gomez", ":prenom"=>"Fanny", ":mail"=>"iaculis.aliquet.diam@nasceturridiculus.org");
$data[]=array(":nom"=>"Guyot", ":prenom"=>"Sara", ":mail"=>"neque.sed@enimgravidasit.co.uk");
$data[]=array(":nom"=>"Martin", ":prenom"=>"Léonard", ":mail"=>"felis.ullamcorper@est.net");
$data[]=array(":nom"=>"Fabre", ":prenom"=>"Cédric", ":mail"=>"feugiat.Lorem.ipsum@metus.com");
$data[]=array(":nom"=>"Fournier", ":prenom"=>"Jeanne", ":mail"=>"Cras@Donec.com");
$data[]=array(":nom"=>"Blanc", ":prenom"=>"Rose", ":mail"=>"nisi.nibh@lorem.ca");
$data[]=array(":nom"=>"Renault", ":prenom"=>"Alexia", ":mail"=>"Sed.congue@tempordiam.com");
$data[]=array(":nom"=>"Denis", ":prenom"=>"Léon", ":mail"=>"facilisis.facilisis@imperdiet.com");
$data[]=array(":nom"=>"Lopez", ":prenom"=>"Margaux", ":mail"=>"Nulla.dignissim.Maecenas@lacus.org");
$data[]=array(":nom"=>"Gomez", ":prenom"=>"Catherine", ":mail"=>"Etiam.imperdiet@arcuVestibulum.com");
$data[]=array(":nom"=>"Nguyen", ":prenom"=>"Kyllian", ":mail"=>"id.mollis@Aeneanegetmagna.edu");
$data[]=array(":nom"=>"Barre", ":prenom"=>"Nathan", ":mail"=>"neque@augueut.ca");
$data[]=array(":nom"=>"Duval", ":prenom"=>"Adrien", ":mail"=>"tellus.id.nunc@Maecenas.net");
$data[]=array(":nom"=>"Garnier", ":prenom"=>"Kilian", ":mail"=>"ornare@aliquet.ca");
$data[]=array(":nom"=>"Louis", ":prenom"=>"Chaïma", ":mail"=>"ipsum.sodales@id.net");
$data[]=array(":nom"=>"Philippe", ":prenom"=>"Yasmine", ":mail"=>"in.magna@DonecnibhQuisque.net");
$data[]=array(":nom"=>"Denis", ":prenom"=>"Nathan", ":mail"=>"vitae.aliquet@congue.co.uk");
$data[]=array(":nom"=>"Barbier", ":prenom"=>"Émilie", ":mail"=>"lorem@dolor.co.uk");
$data[]=array(":nom"=>"Fournier", ":prenom"=>"Léonard", ":mail"=>"nibh.sit.amet@est.org");
$data[]=array(":nom"=>"Morin", ":prenom"=>"Émile", ":mail"=>"Curabitur.massa@scelerisqueloremipsum.edu");
$data[]=array(":nom"=>"Carpentier", ":prenom"=>"Mélanie", ":mail"=>"turpis.egestas.Aliquam@volutpatornare.edu");
$data[]=array(":nom"=>"Herve", ":prenom"=>"Françoise", ":mail"=>"quis.massa@Aliquam.org");
$data[]=array(":nom"=>"Roy", ":prenom"=>"Guillaume", ":mail"=>"Nunc.ac@vitaeodiosagittis.edu");
$data[]=array(":nom"=>"Nguyen", ":prenom"=>"Renaud", ":mail"=>"Donec.tempus@necante.co.uk");
$data[]=array(":nom"=>"Mallet", ":prenom"=>"Lou", ":mail"=>"ac@enimmi.co.uk");
$data[]=array(":nom"=>"Prevost", ":prenom"=>"Titouan", ":mail"=>"cubilia.Curae@a.co.uk");
$data[]=array(":nom"=>"Meunier", ":prenom"=>"Mathéo", ":mail"=>"hendrerit@ac.org");
$data[]=array(":nom"=>"Bailly", ":prenom"=>"Tatiana", ":mail"=>"interdum.ligula@famesac.edu");
$data[]=array(":nom"=>"Berger", ":prenom"=>"Amélie", ":mail"=>"eu.nulla.at@utmi.net");
$data[]=array(":nom"=>"Pereira", ":prenom"=>"Zoé", ":mail"=>"ultricies.dignissim.lacus@quamquis.ca");
$data[]=array(":nom"=>"Brun", ":prenom"=>"Julie", ":mail"=>"rhoncus.Nullam@quis.net");
$data[]=array(":nom"=>"Dumont", ":prenom"=>"Alexandre", ":mail"=>"Etiam@dapibus.net");
$data[]=array(":nom"=>"Leroy", ":prenom"=>"Maryam", ":mail"=>"ante.iaculis@mattisvelitjusto.net");
$data[]=array(":nom"=>"Mercier", ":prenom"=>"Alexis", ":mail"=>"suscipit.nonummy.Fusce@commodohendreritDonec.co.uk");
$data[]=array(":nom"=>"Robin", ":prenom"=>"Bienvenue", ":mail"=>"interdum.libero.dui@vitaesemper.com");
$data[]=array(":nom"=>"Gerard", ":prenom"=>"Amine", ":mail"=>"Sed.eget.lacus@Crasdolor.org");
$data[]=array(":nom"=>"Bailly", ":prenom"=>"Gaspard", ":mail"=>"velit.Sed.malesuada@quamdignissimpharetra.ca");
$data[]=array(":nom"=>"Marchal", ":prenom"=>"Kevin", ":mail"=>"nisi.nibh.lacinia@vitaeodio.ca");
$data[]=array(":nom"=>"Meunier", ":prenom"=>"Erwan", ":mail"=>"pede.nec@arcu.com");
$data[]=array(":nom"=>"Berger", ":prenom"=>"Lena", ":mail"=>"dui.Cum.sociis@eratnequenon.com");
$data[]=array(":nom"=>"Pierre", ":prenom"=>"Malo", ":mail"=>"sollicitudin.commodo.ipsum@eu.co.uk");
$data[]=array(":nom"=>"Simon", ":prenom"=>"Léonard", ":mail"=>"velit@hendreritid.co.uk");
$data[]=array(":nom"=>"Lemoine", ":prenom"=>"Agathe", ":mail"=>"ac.ipsum.Phasellus@Suspendisse.com");
$data[]=array(":nom"=>"Meunier", ":prenom"=>"Mélanie", ":mail"=>"sit.amet.dapibus@libero.com");
$data[]=array(":nom"=>"Jean", ":prenom"=>"Margot", ":mail"=>"id@Suspendisse.com");
$data[]=array(":nom"=>"Collet", ":prenom"=>"Benjamin", ":mail"=>"eu@PraesentluctusCurabitur.co.uk");
$data[]=array(":nom"=>"nom", ":prenom"=>"prenom", ":mail"=>"email");
$data[]=array(":nom"=>"Colin", ":prenom"=>"Anaëlle", ":mail"=>"sapien.gravida.non@vel.ca");
$data[]=array(":nom"=>"Benoit", ":prenom"=>"Marion", ":mail"=>"mattis.ornare@nonante.org");
$data[]=array(":nom"=>"Andre", ":prenom"=>"Malik", ":mail"=>"tellus.Nunc@sitametlorem.net");
$data[]=array(":nom"=>"Fournier", ":prenom"=>"Guillaume", ":mail"=>"semper.pretium@elitCurabitur.com");
$data[]=array(":nom"=>"Daniel", ":prenom"=>"Justine", ":mail"=>"Nullam.vitae@justoPraesent.co.uk");
$data[]=array(":nom"=>"Dupuy", ":prenom"=>"Catherine", ":mail"=>"Vivamus.rhoncus@sociis.org");
$data[]=array(":nom"=>"Boulanger", ":prenom"=>"Mathis", ":mail"=>"vel.sapien@ultrices.ca");
$data[]=array(":nom"=>"Germain", ":prenom"=>"Chloé", ":mail"=>"lorem.auctor@auctorullamcorpernisl.co.uk");
$data[]=array(":nom"=>"Collin", ":prenom"=>"Pierre", ":mail"=>"scelerisque.neque.Nullam@neque.org");
$data[]=array(":nom"=>"Renaud", ":prenom"=>"Paul", ":mail"=>"sodales.nisi@lectus.edu");
$data[]=array(":nom"=>"Sanchez", ":prenom"=>"Yasmine", ":mail"=>"nisl@faucibusutnulla.com");
$data[]=array(":nom"=>"Lacroix", ":prenom"=>"Louis", ":mail"=>"Suspendisse@tristiquepharetra.ca");
$data[]=array(":nom"=>"Dumas", ":prenom"=>"Yasmine", ":mail"=>"Sed.nulla.ante@Quisqueliberolacus.ca");
$data[]=array(":nom"=>"Mercier", ":prenom"=>"Mehdi", ":mail"=>"eu.dui@et.edu");
$data[]=array(":nom"=>"Olivier", ":prenom"=>"Amélie", ":mail"=>"purus.gravida@nonhendreritid.co.uk");
$data[]=array(":nom"=>"Collet", ":prenom"=>"Lena", ":mail"=>"mi@justoPraesent.edu");
$data[]=array(":nom"=>"Henry", ":prenom"=>"Nina", ":mail"=>"egestas.hendrerit@nonummy.edu");
$data[]=array(":nom"=>"Paris", ":prenom"=>"Lana", ":mail"=>"Maecenas.ornare.egestas@tincidunt.ca");
$data[]=array(":nom"=>"Guillaume", ":prenom"=>"Noah", ":mail"=>"erat@odioNaminterdum.com");
$data[]=array(":nom"=>"Le goff", ":prenom"=>"Maïlé", ":mail"=>"porttitor@utodiovel.co.uk");
$data[]=array(":nom"=>"Julien", ":prenom"=>"Bienvenue", ":mail"=>"magna@pedenonummy.edu");
$data[]=array(":nom"=>"Clement", ":prenom"=>"Lou", ":mail"=>"rhoncus.Donec@ipsumprimisin.com");
$data[]=array(":nom"=>"Paris", ":prenom"=>"Bienvenue", ":mail"=>"iaculis@Suspendissealiquet.org");
$data[]=array(":nom"=>"Blanc", ":prenom"=>"Adrien", ":mail"=>"nulla.Donec@telluslorem.org");
$data[]=array(":nom"=>"Renault", ":prenom"=>"Salomé", ":mail"=>"nisl@porttitortellusnon.ca");
$data[]=array(":nom"=>"Roche", ":prenom"=>"Victor", ":mail"=>"est.Mauris.eu@eudoloregestas.co.uk");
$data[]=array(":nom"=>"Guerin", ":prenom"=>"Élise", ":mail"=>"arcu@non.com");
$data[]=array(":nom"=>"Gillet", ":prenom"=>"Maryam", ":mail"=>"ut.odio.vel@liberoDonec.edu");
$data[]=array(":nom"=>"Denis", ":prenom"=>"Lisa", ":mail"=>"vitae.sodales@risusNulla.net");
$data[]=array(":nom"=>"Millet", ":prenom"=>"Baptiste", ":mail"=>"Duis.volutpat@nisl.net");
$data[]=array(":nom"=>"Giraud", ":prenom"=>"Manon", ":mail"=>"nisl.Maecenas.malesuada@Nuncsollicitudincommodo.ca");
$data[]=array(":nom"=>"Rodriguez", ":prenom"=>"Jules", ":mail"=>"Suspendisse@vitae.co.uk");
$data[]=array(":nom"=>"Francois", ":prenom"=>"Bastien", ":mail"=>"non.lacinia@utmolestiein.co.uk");
$data[]=array(":nom"=>"Henry", ":prenom"=>"Pierre", ":mail"=>"ullamcorper@rutrumnon.co.uk");
$data[]=array(":nom"=>"Breton", ":prenom"=>"Carla", ":mail"=>"dapibus.quam.quis@nuncsitamet.net");
$data[]=array(":nom"=>"Blanc", ":prenom"=>"Mehdi", ":mail"=>"ultricies.dignissim.lacus@sedhendrerita.com");
$data[]=array(":nom"=>"Perrin", ":prenom"=>"Tatiana", ":mail"=>"dictum.cursus.Nunc@eget.net");
$data[]=array(":nom"=>"Pons", ":prenom"=>"Benjamin", ":mail"=>"consequat.purus@cursusin.co.uk");
$data[]=array(":nom"=>"Julien", ":prenom"=>"Timothée", ":mail"=>"eu.odio@velpedeblandit.org");
$data[]=array(":nom"=>"Jean", ":prenom"=>"Timothée", ":mail"=>"at@Vivamusnibhdolor.co.uk");
$data[]=array(":nom"=>"Poulain", ":prenom"=>"Elsa", ":mail"=>"placerat.velit.Quisque@velitdui.net");
$data[]=array(":nom"=>"Colin", ":prenom"=>"Roméo", ":mail"=>"dui@tinciduntpedeac.ca");
$data[]=array(":nom"=>"Rolland", ":prenom"=>"Florentin", ":mail"=>"mi@eget.ca");
$data[]=array(":nom"=>"Muller", ":prenom"=>"Nathan", ":mail"=>"dui.nec@lobortisClassaptent.edu");
$data[]=array(":nom"=>"Gay", ":prenom"=>"Élisa", ":mail"=>"amet.risus@Donecnibh.edu");
$data[]=array(":nom"=>"Le roux", ":prenom"=>"Pierre", ":mail"=>"Proin.velit.Sed@aultriciesadipiscing.net");
$data[]=array(":nom"=>"Morel", ":prenom"=>"Antoine", ":mail"=>"nunc.ullamcorper@sit.org");
$data[]=array(":nom"=>"Paris", ":prenom"=>"Cloé", ":mail"=>"vehicula@estMauris.co.uk");
$data[]=array(":nom"=>"Bernard", ":prenom"=>"Titouan", ":mail"=>"Quisque.ac@Sedeu.edu");
$data[]=array(":nom"=>"Benoit", ":prenom"=>"Justine", ":mail"=>"odio@dictumultricies.org");
$data[]=array(":nom"=>"Dupuy", ":prenom"=>"Louna", ":mail"=>"lacus@fringilla.ca");
$data[]=array(":nom"=>"Rolland", ":prenom"=>"Margot", ":mail"=>"sit.amet.ultricies@Maecenas.org");
$data[]=array(":nom"=>"Jacob", ":prenom"=>"Nolan", ":mail"=>"ac@eutellus.com");
$data[]=array(":nom"=>"Gay", ":prenom"=>"Maïwenn", ":mail"=>"ac.ipsum@lectusjustoeu.co.uk");
$data[]=array(":nom"=>"Leveque", ":prenom"=>"Élise", ":mail"=>"pede.nonummy@elit.org");
$data[]=array(":nom"=>"Rey", ":prenom"=>"Tom", ":mail"=>"pede@acnulla.com");
$data[]=array(":nom"=>"Dumas", ":prenom"=>"Malik", ":mail"=>"Sed.eu.eros@sempertellusid.co.uk");
$data[]=array(":nom"=>"Leclercq", ":prenom"=>"Mathéo", ":mail"=>"ligula.tortor@nibhsitamet.net");
$data[]=array(":nom"=>"Paris", ":prenom"=>"Éloïse", ":mail"=>"dolor.Nulla@velvenenatis.ca");
$data[]=array(":nom"=>"Philippe", ":prenom"=>"Louna", ":mail"=>"ac.eleifend@auctorquistristique.org");
$data[]=array(":nom"=>"Schneider", ":prenom"=>"Dylan", ":mail"=>"Class.aptent.taciti@Praesenteudui.co.uk");
$data[]=array(":nom"=>"Hubert", ":prenom"=>"Éloïse", ":mail"=>"risus.Nulla@purusactellus.ca");
$data[]=array(":nom"=>"Mathieu", ":prenom"=>"Alicia", ":mail"=>"mauris.sit@sodalesat.net");
$data[]=array(":nom"=>"Vidal", ":prenom"=>"Tatiana", ":mail"=>"consectetuer@elit.net");
$data[]=array(":nom"=>"Bourgeois", ":prenom"=>"Mélissa", ":mail"=>"accumsan.interdum@Suspendissealiquet.ca");
$data[]=array(":nom"=>"Clement", ":prenom"=>"Agathe", ":mail"=>"aliquam@sedconsequat.com");
$data[]=array(":nom"=>"Le roux", ":prenom"=>"Lena", ":mail"=>"ut.quam@nisl.org");
$data[]=array(":nom"=>"Lefebvre", ":prenom"=>"Quentin", ":mail"=>"lorem@etmalesuadafames.co.uk");
$data[]=array(":nom"=>"Aubert", ":prenom"=>"Corentin", ":mail"=>"magna.Ut@justo.edu");
$data[]=array(":nom"=>"Daniel", ":prenom"=>"Edwige", ":mail"=>"sem@necenim.co.uk");
$data[]=array(":nom"=>"Leroy", ":prenom"=>"Ethan", ":mail"=>"blandit.viverra@ametconsectetueradipiscing.ca");
$data[]=array(":nom"=>"Leveque", ":prenom"=>"Mathéo", ":mail"=>"vitae@nonquamPellentesque.ca");
$data[]=array(":nom"=>"Jacob", ":prenom"=>"Maxime", ":mail"=>"felis.ullamcorper@ametconsectetueradipiscing.co.uk");
$data[]=array(":nom"=>"Boucher", ":prenom"=>"Cédric", ":mail"=>"sit.amet@molestietellus.net");
$data[]=array(":nom"=>"Guyot", ":prenom"=>"Diego", ":mail"=>"et.eros.Proin@feugiatnonlobortis.co.uk");
$data[]=array(":nom"=>"Rousseau", ":prenom"=>"Mohamed", ":mail"=>"per.inceptos.hymenaeos@nascetur.org");
$data[]=array(":nom"=>"Herve", ":prenom"=>"Ambre", ":mail"=>"lobortis@dictumeleifendnunc.com");
$data[]=array(":nom"=>"Philippe", ":prenom"=>"Pauline", ":mail"=>"non.massa@Donec.co.uk");
$data[]=array(":nom"=>"Mercier", ":prenom"=>"Cloé", ":mail"=>"penatibus@Praesenteunulla.com");
$data[]=array(":nom"=>"Renaud", ":prenom"=>"Mélanie", ":mail"=>"ullamcorper.viverra@pedenec.com");
$data[]=array(":nom"=>"Robert", ":prenom"=>"Mathieu", ":mail"=>"pellentesque@Craseutellus.org");
$data[]=array(":nom"=>"Garnier", ":prenom"=>"Anaëlle", ":mail"=>"ut.nulla.Cras@faucibus.net");
$data[]=array(":nom"=>"Schmitt", ":prenom"=>"Lauriane", ":mail"=>"Nunc.lectus.pede@infelisNulla.org");
$data[]=array(":nom"=>"Jean", ":prenom"=>"Justine", ":mail"=>"Proin.ultrices.Duis@ipsumleo.net");
$data[]=array(":nom"=>"Guillaume", ":prenom"=>"Amine", ":mail"=>"cursus.Nunc.mauris@Vivamussit.net");
$data[]=array(":nom"=>"Boulanger", ":prenom"=>"Yasmine", ":mail"=>"egestas.Sed@dolorFuscefeugiat.ca");
$data[]=array(":nom"=>"Andre", ":prenom"=>"Léonie", ":mail"=>"elit@ac.ca");
$data[]=array(":nom"=>"Lopez", ":prenom"=>"Romane", ":mail"=>"malesuada.Integer.id@egestas.com");
$data[]=array(":nom"=>"Menard", ":prenom"=>"Yasmine", ":mail"=>"Sed.diam@Innec.net");
$data[]=array(":nom"=>"Dumont", ":prenom"=>"Sarah", ":mail"=>"libero.est@arcuMorbi.com");
$data[]=array(":nom"=>"Blanc", ":prenom"=>"Léa", ":mail"=>"Morbi.quis@urnasuscipitnonummy.edu");
$data[]=array(":nom"=>"Guillaume", ":prenom"=>"Clara", ":mail"=>"purus@Curabiturut.com");
$data[]=array(":nom"=>"Marie", ":prenom"=>"Marine", ":mail"=>"ultrices@commodo.edu");
$data[]=array(":nom"=>"Leclerc", ":prenom"=>"Robin", ":mail"=>"libero@nislsemconsequat.net");
$data[]=array(":nom"=>"Gautier", ":prenom"=>"Maxence", ":mail"=>"justo.faucibus.lectus@rutrum.co.uk");
$data[]=array(":nom"=>"Perez", ":prenom"=>"Mohamed", ":mail"=>"sodales.elit.erat@eros.org");
$data[]=array(":nom"=>"Richard", ":prenom"=>"Léonie", ":mail"=>"dui.quis@quam.org");
$data[]=array(":nom"=>"Boucher", ":prenom"=>"Mathieu", ":mail"=>"vel@Nullamvelitdui.ca");
$data[]=array(":nom"=>"Vidal", ":prenom"=>"Maxence", ":mail"=>"vitae.purus@interdum.org");
$data[]=array(":nom"=>"Fabre", ":prenom"=>"Mohamed", ":mail"=>"eget.volutpat@tempusloremfringilla.ca");

$db=new db();
$db->select2("personnel");

$db2=new dbh();
$db2->prepare("UPDATE `personnel` SET nom=:nom, prenom=:prenom, mail=:mail, mailsResponsables=:mailsResponsables 
  ,password=md5('password'), login=:login WHERE id=:id;");

$i=0;
foreach($data as $elem){

$id=$db->result[$i]['id'];
  
  if($id==2 or $i>=count($db->result)){
    $i++;
    continue;
  }

  $data[$i][":mailsResponsables"]=$mailsResponsables;
  
  $data[$i][":login"]="login".$id;
  if($id==1){
    $data[$i][":login"]="admin";
  }
  $data[$i][":id"]=$id;

  print_r($data[$i]);
  echo "<br/>";
  $db2->execute($data[$i]);
  
  $i++;
}



?>