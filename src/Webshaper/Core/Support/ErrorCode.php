<?php namespace Webshaper\Core\Support;

class ErrorCode {
    const STORE_NOT_FOUND = "Store does not exists";
    const SETUP_STORE_NOT_FOUND = "Store Information not correct";
    const TRIAL_STORE_NOT_EXISTS = "Trial store not found";
    const AUTHENTICATE_FAILED = "Authentication failed";
    const PRODUCT_QUANTITY_INSUFFICIENT = "Insufficient stock";
    const ORDER_ALREADY_CANCELLED = "Order already cancelled";
}