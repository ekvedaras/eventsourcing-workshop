<?php

namespace App\Http\Livewire;

use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\ReadModels\Transaction;
use Livewire\Component;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\WalletId;

class Transactions extends Component
{
    public string $walletId;

    public $tokens = 0;
    public $description = '';

    public int $balance = 0;

    protected $rules = [
        'tokens' => 'required|integer|min:1',
    ];

    public function mount(string $walletId, WalletBalanceRepository $balanceRepository)
    {
        // check if wallet id can be parsed.
        $id = WalletId::fromString($walletId);
        $this->walletId = $walletId;
        $this->balance = $balanceRepository->getBalance($id);
    }

    public function deposit(WalletRepository $walletRepository, WalletBalanceRepository $balanceRepository)
    {

        $wallet = $walletRepository->retrieve(WalletId::fromString($this->walletId));
        $wallet->deposit($this->tokens, $this->description);
        $walletRepository->persist($wallet);
        $this->balance = $balanceRepository->getBalance($wallet->aggregateRootId());

        $this->tokens = 0;
        $this->description = '';
        session()->flash('success', 'Money successfully deposited.');
    }

    public function withdraw(WalletRepository $walletRepository, WalletBalanceRepository $balanceRepository)
    {
        $wallet = $walletRepository->retrieve(WalletId::fromString($this->walletId));
        $wallet->withdraw($this->tokens, $this->description);
        $walletRepository->persist($wallet);
        $this->balance = $balanceRepository->getBalance($wallet->aggregateRootId());

        $this->tokens = 0;
        $this->description = '';
        session()->flash('success', 'Money successfully withdrawn.');
    }

    public function dismiss()
    {
        session()->forget('success');
    }

    public function render()
    {
        return view('livewire.transactions', [
            'transactions' => Transaction::forWallet($this->walletId)->orderBy('transacted_at', 'desc')->paginate(10),
        ]);
    }
}
