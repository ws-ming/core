<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Models\Customer;

class CustomerRepository extends BaseRepository{

    function __construct(Customer $model){
        parent::__construct($model);
    }

    public function getCustomerByEmail($email)
    {
        $customer = $this->model->where('txtCustomerEmail',trim($email))->get();
        if(count($customer) ===0) return null;
        return $customer[0];
    }

    public function searchCustomer($keywords)
    {
        $columns = array(
            'txtFirstName',
            'txtLastName',
            'txtCustomerEmail',
        );

        return $this->search($keywords,$columns);
    }


}