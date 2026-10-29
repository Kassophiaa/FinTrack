<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/BankAccount.php';

$active = "income";

$accounts = (new BankAccount())->getAccounts($_SESSION['user_id']);

$tx = [
    'module' => 'income',
    'id_field' => 'income_id',
    'type_field' => 'income_type',
    'date_field' => 'income_date',
    'noun' => 'income',
    'plural_title' => 'Income',
    'intro' => 'Record money coming in, like salary, allowance or side jobs.',
    'type_label' => 'Type',
    'account_label' => 'Received into',
    'desc_placeholder' => 'e.g. October salary',
    'types' => ['Salary', 'Allowance', 'Freelance', 'Business', 'Commission', 'Bonus', 'Investment Income'],
    'icon' => 'bi-arrow-down-left',
    'chip' => 'chip-success',
    'sign' => '+',
];

$title = "Income";
include '../includes/header.php';
include '../includes/navbar.php';
include '../includes/transaction_page.php';
