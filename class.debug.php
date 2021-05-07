<?php
class Debug{

    public $level='all';//all, 
    public $backtrace=0;
    public $count;
    public $to='someone@somehere.com';
    public $message='';//can be string or array
    public $subject='Debug Email - ';
    public $from='noone@nowhere.com';
    public $replyto='noone@nowhere.com';
    public $headers='';//all or nothing
    public $fromname = "PHP Engine";

    public function __construct(){

    }
    public function __destruct(){
    }
    
    public function now(){
        return time();
    }
    
    public function headers(){
        //probably should add a little injection protection here
        //...But is an admin debug method, so meh

        if(empty($this->headers)){
            $this->headers = 'To: Admin <'.$this->to.'>' . "\r\n";
            $this->headers .= "MIME-Version: 1.0" . "\r\n";
            $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $this->headers .= "From:  Debug email <".$this->from."> \r\n";
            $this->headers .= "Reply-To:  ".$this->replyto."\r\n".'X-Mailer: PHP/' . phpversion();
        }
        return $this->headers;//necessary? >>test
    }
    
    public function dmail(){
        /**
         * Debug mailer
         * create the to/from/subject/message/headers variables before calling the method
         * update defaults to actual emails
         * time() creates unique subject to prevent email clients like gmail, grouping debug mails into a conversation
         */
        //message 
    
        
        $this->headers=$this->headers();

        if(!empty($this->message)){
            if(is_array($this->message)){
                $message=$this->prs($this->message,1);
                $message=$message."<br/><br/>"; //adda space before dumping backtrace data
            }
        }
        
        $details=debug_backtrace();
        foreach($details as $k=>$v){
            foreach($details as $k2=>$v2){
                if($k2=='file'){
                    $message.='<p>';
                    $message.= 'File - '.$v['file'];
                    $message.= '<br />Line - '.$v['line'];
                    $message.= '</p>';
                }
            }
        }
        
        $html_message = '<html><body>';
        $html_message .=$message;
        $html_message .= '</body></html>';

        mail($this->to,$this->subject.' - '.time(),$html_message,$this->headers);
    }
    
    /*display tools*/
    private function prep_display_array($arr,$count){
        //display_array()'s worker bee function
        //style not classes (for email use)
        ksort($arr);
        if(is_array($arr)){
            foreach($arr as $k=> $v){
                $margin=$count*20;
                if(is_array($v)){
                    echo '  <div style="margin-bottom:3px;margin-left:'.$margin.'px;">['.$k.']=>Array</div>
                            <div style="clear:both"></div>';
                            $count++;
                            $this->prep_display_array($v,$count);
                            $count--;
                }else{
                    echo '  <div style="margin-bottom:3px;margin-left:'.$margin.'px;">['.$k.']=>'.$v.'</div>
                            <div style="clear:both"></div>';
                }
            }
        }else{
            return 'Not an array';  
        }
    }
    
    public function prs($v='',$return=0){
        //recursive array display buffered and returned
        
        if(is_array($v)){
            ob_start();
                $this->prep_display_array($v,1);
            $retstr= ob_get_clean();

        }else{
            $retstr= '<div style="margin:5px 0;">'.$v.'&nbsp;</div>';
        }

        if(empty($return)){
            echo $retstr;
        }else{
            return $retstr; 
        }
    }

    public function pr($v,$return=0){
        //formatted and extended print_r()
        $this->prs($v);
        $details=debug_backtrace();
        foreach($details as $k=>$v){
            foreach($details as $k2=>$v2){
                if($k2=='file'){
                    echo '<p>';
                    echo 'File - '.$v['file'];
                    echo '<br />Line - '.$v['line'];
                    echo '</p>';
                }
            }
        }

        if($this->backtrace==1)$this->pb();

    }

    public function pb(){
        $details=debug_backtrace();
        $this->pr($details);
    }
    
    public function line(){ 
        $details=debug_backtrace();
        echo '<br/>'.$details[0]['line'].'<br/>';
    }

}


