<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/BankAccount.php';

$active = "expenses";

$accounts = (new BankAccount())->getAccounts($_SESSION['user_id']);

$tx = [
    'module' => 'expense',
    'id_field' => 'expense_id',
    'type_field' => 'expense_type',
    'date_field' => 'expense_date',
    'noun' => 'expense',
    'plural_title' => 'Expenses',
    'intro' => 'Keep track of what you spend so you can see where your money goes.',
    'type_label' => 'Category',
    'account_label' => 'Paid from',
    'desc_placeholder' => 'e.g. Groceries at SM',
    'types' => ['Food', 'Transportation', 'Housing', 'Utilities', 'Education', 'Healthcare', 'Entertainment', 'Shopping', 'Bills'],
    'icon' => 'bi-arrow-up-right',
    'chip' => 'chip-danger',
    'sign' => '-',
];

$title = "Expenses";
include '../includes/header.php';
include '../includes/navbar.php';
include '../includes/transaction_page.php';
