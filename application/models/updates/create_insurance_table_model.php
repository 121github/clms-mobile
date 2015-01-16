<?php

class Create_insurance_table_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
    public function create(){
//create the insurance table
$create = "CREATE TABLE IF NOT EXISTS `insurers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `insurer` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=106" ;
$this->db->query($create);
    }

     public function populate(){
//populate the insurance table
$populate = "REPLACE INTO `insurers` (`id`, `insurer`) VALUES
(1, '1st Central'),
(2, 'Abbey Life'),
(3, 'Admiral Group'),
(4, 'Admiral Insurance'),
(5, 'Adrian Flux Insurance Services'),
(6, 'Allied Dunbar'),
(7, 'Amlin'),
(8, 'Ansvar insurance'),
(9, 'Aon (company)'),
(10, 'Association of British Insurers'),
(11, 'Aviva'),
(12, 'AXA PPP healthcare'),
(13, 'Beazley Group'),
(14, 'Benfield Group'),
(15, 'Bennetts'),
(16, 'BGL Group'),
(17, 'Brand Partners'),
(18, 'Bridle Insurance'),
(19, 'Brightside Group'),
(20, 'Brit Insurance'),
(21, 'British Insurance'),
(22, 'Camberford Law'),
(23, 'Carole Nash'),
(24, 'CGU plc'),
(25, 'Chaucer Holdings'),
(26, 'ChoiceQuote Insurance Services'),
(27, 'Churchill Insurance Company'),
(28, 'Clerical Medical'),
(29, 'Combined Insurance'),
(30, 'Cornish Mutual'),
(31, 'Countrywide Legal Indemnities'),
(32, 'Daily Mail and General Trust'),
(33, 'Devitt Insurance'),
(34, 'Direct Line'),
(35, 'Drakefield Insurance'),
(36, 'Eagle Star Insurance'),
(37, 'Ecclesiastical Insurance'),
(38, 'Elephant.co.uk'),
(39, 'Endsleigh Insurance'),
(40, 'Engage Mutual Assurance'),
(41, 'The Equitable Life Assurance Society'),
(42, 'Equitas'),
(43, 'Equity Insurance Group'),
(44, 'Esure'),
(45, 'Excel Insurance Solutions'),
(46, 'Friends Provident'),
(47, 'Antony Gibbs & Sons'),
(48, 'Guardian Assurance Company'),
(49, 'Guardian Royal Exchange Assurance'),
(50, 'Hand in Hand Fire & Life Insurance Society'),
(51, 'Hastings Direct'),
(52, 'Health-on-Line'),
(53, 'Helpucover'),
(54, 'Howden Insurance Brokers Limited'),
(55, 'InsureandGo'),
(56, 'Kwelm'),
(57, 'Kwik Fit Insurance'),
(58, 'Legal & General'),
(59, 'Lifesure Insurance Group'),
(60, 'Liverpool Victoria'),
(61, 'Lloyd''s Agency Network'),
(62, 'Markerstudy Group'),
(63, 'Motor Insurers'' Bureau'),
(64, 'Municipal Mutual Insurance'),
(65, 'NFU Mutual'),
(66, 'North British and Mercantile Insurance'),
(67, 'North of England P&I Association'),
(68, 'Norwich Union'),
(69, 'Novae Group'),
(70, 'Old Mutual'),
(71, 'UIA (Insurance)'),
(72, 'Westfield Health'),
(73, 'Willis Group'),
(74, 'Young Marmalade'),
(75, 'Zurich Insurance Group'),
(76, 'Pavilion Insurance'),
(77, 'Phoenix Fire Office'),
(78, 'Phoenix Group'),
(79, 'Police Mutual'),
(80, 'Pool Re'),
(81, 'Professional Insurance Agents'),
(82, 'Prudential plc'),
(83, 'PruHealth'),
(84, 'Refuge Assurance Company'),
(85, 'Resolution plc'),
(86, 'Royal Exchange Assurance Corporation'),
(87, 'Royal Liver Assurance'),
(88, 'Royal London Group'),
(89, 'RSA Insurance Group'),
(90, 'Saffron Insurance Services Ltd'),
(91, 'Scottish Friendly'),
(92, 'Scottish Widows'),
(93, 'Sedgwick Group'),
(94, 'Simply Business'),
(95, 'Standard Life Healthcare'),
(96, 'Stonebridge International Insurance Ltd'),
(97, 'Suffolk Life'),
(98, 'Sun Life & Provincial Holdings'),
(99, 'Sun Life Financial'),
(100, 'Sureterm'),
(101, 'Swiftcover'),
(102, 'Swinton Insurance'),
(103, 'The Co-operative Insurance'),
(104, 'Totally Insured'),
(105, 'Towergate Partnership')";
if ($this->db->query($populate)):
$msg  = "insurance table was created and populated with default insurers";
else:
$msg = "There was an error creating the insurance table";
endif;

return array("action"=>"populate insurance table","msg"=>$msg);

    }
    
}
?>