<div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6">
        <div class="mx-auto max-w-6xl">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-xl font-semibold text-gray-900">Transactions</h1>
                        <p class="mt-2 text-sm text-gray-700">A list of all transactions for this wallet</p>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex items-baseline space-x-4 flex-1">
                        <div class="flex items-baseline flex-1">
                            <div class="text-xl font-semibold {{ $this->balance > 0 ? 'text-green-600' : 'text-red-500' }} p-0">{{ $this->balance }}</div>
                            <div class="ml-2 text-sm">{{ str('token')->plural($this->balance) }}</div>
                        </div>
                        <div class="inline-flex ml-2">
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="inline-flex">
                                    <input wire:model="description" type="text" name="description" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="description">
                                    <input wire:model="tokens" type="number" name="price" id="price" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0" aria-describedby="price-currency">
                                </div>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 sm:text-sm" id="price-currency">Tokens</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" wire:click.prevent="deposit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">Add tokens</button>
                        <button type="button" wire:click.prevent="withdraw" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">Withdraw tokens</button>
                    </div>
                </div>
                <div class="mt-8 flex flex-col">
                    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Description</th>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Amount</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">date</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                {{$transaction->description}}
                                            </td>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium @if($transaction->amount < 0 ) text-red-600 @else text-gray-900 @endif sm:pl-6">
                                                {{$transaction->amount}}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$transaction->transacted_at->diffForHumans()}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="p-2">{{ $transactions->links() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--    notifications --}}
    <div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            @if (session()->has('success'))
                <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">Success!</p>
                                <p class="mt-1 text-sm text-gray-500">{{ session('success') }}</p>
                            </div>
                            <div class="ml-4 flex flex-shrink-0">
                                <button wire:click.prevent="dismiss" type="button" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="sr-only" >Close</span>
                                    <!-- Heroicon name: mini/x-mark -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
