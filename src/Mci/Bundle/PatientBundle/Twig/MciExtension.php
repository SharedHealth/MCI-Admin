<?php
namespace Mci\Bundle\PatientBundle\Twig;



class MciExtension extends \Twig_Extension
{
    public $gender = array('M'=>'Male','F'=>'Female','O'=>'Others');
    public $occupation = array(
        '01'=>'ভৌত বিজ্ঞানী এবং এতদ সম্পর্কিত টেকনিশিয়ান',
        '02'=>'ইঞ্জিনিয়ারিং ও স্থপতি',
        '03' => 'ইঞ্জিনিয়ারিং ও স্থপতি সম্পর্কিত টেকনিশিয়ান',
        '04' => 'বিমান এবং জাহাজের কর্মকর্তা',
        '05' => 'জীব বিজ্ঞানী এবং এতদ সম্পর্কিত টেকনিশিয়ান',
        '06' => 'চিকিৎসক, দন্ত চিকিৎসক ও পশু চিকিৎসক',
        '07' => 'নার্স এবং চিকিৎসক সংক্রান্ত অন্যান্য কর্মী',
        '08' => 'পরিসংখ্যানবিদ, গণিতবিদ, সিষ্টেম এনালিষ্ট এবং এতদসম্পর্কিত কর্মী',
        '09' =>'অর্থনীতিবিদ',
        '10'=> 'হিসাবরক্ষক',
        '12' => 'বিচারক ',
        '13' => 'শিক্ষক',
        '14' => 'ধর্মীয় কর্মী',
        '15' => 'লেখক, সাংবাদিক এবং এতদস সম্পর্কিত কর্মী',
        '17' => 'অভিনয়, কন্ঠ শিল্পী ও নৃত্য শিল্পী',
        '16' => 'চিত্র শিল্পী, ফটোগ্রাফার এবং এতদসম্পর্কিত সৃজনশীল শিল্পী',
        '18' => 'খেলোয়াড় এবং এতদসম্পর্কিত কর্মী',
    );

    public $eduLevel = array(
        '00' => 'নার্সারি/কিন্ডার গার্ডেন',
        '01' => '১ম শ্রেনী',
        '02' => '২য় শ্রেনী',
        '03' => '৩য় শ্রেনী',
        '04' => '৪র্থ শ্রেনী',
        '05' => '৫ম শ্রেনী',
        '06' => '৬ষ্ঠ শ্রেনী',
        '07' => '৭ম শ্রেনী',
        '08' => '৮ম শ্রেনী',
        '09' => '৯ম শ্রেনী',
        '10' => '১০ম শ্রেনী বা সমতুল্য',
        '11' => 'উচ্চ মাধ্যমিক বা সমতুল্য',
        '12' => 'স্মাতক বা সমতুল্য',
        '14' => 'ডাক্তারি'
    );

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('gender', array($this, 'genderFilter')),
            new \Twig_SimpleFilter('occupation', array($this, 'occupationFilter')),
            new \Twig_SimpleFilter('edulevel', array($this, 'eduLevelFilter')),
        );
    }

    public function genderFilter($number)
    {
        return $this->gender[$number];
    }

    public function occupationFilter($number){
        return $this->occupation[$number];
    }

    public function eduLevelFilter($number){
        return $this->eduLevel[$number];
    }

    public function getName()
    {
        return 'mci_extension';
    }
}
?>
