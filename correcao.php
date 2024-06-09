<x-app-layout>
    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('Lista de Atendimentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                    <!-- Área de pesquisa de pacientes -->
                    <div class="w-1/4 block">
                        <form action="{{ route('dashboard') }}" method="GET">
                            <input type="text" id="searchInput3" name="search"
                            class="border-gray-300 rounded-md px-4 py-2 w-full bg-white border shadow-sm border-slate-300 
                            placeholder-slate-400 focus:outline-none focus:border-green-800 focus:ring-green-800 block 
                            w-full rounded-md sm:text-sm focus:ring-1 focus:ring-opacity-50" placeholder="Nome ou Cartão SUS...">

                            <button class="d-none" type="submit">Search</button>
                        </form>
                    </div>
                    <!-- Ver somente meus atendimentos switch -->
                    <div class="">
                        <form class="flex items-center ml-2" id="dashboardForm" action="{{ route('dashboard') }}" method="GET">
                            <label class="switch flex items-center justify-center">
                                <input type="checkbox" id="switchVerAtendimentos" onchange="meusEncaminhamentos('{{ \Illuminate\Support\Facades\Auth::user()->attention_type }}')">
                                <span class="slider"></span>
                            </label>
                            <span class="ml-2 text-gray-800">Ver somente meus atendimentos</span>
                        </form>
                    </div>
                    <!-- Botão Novo paciente -->
                    <button type="button" class="icon text-white font-bold py-2 px-4 rounded shadow-md" style="background-color: #186f65;" data-toggle="modal" data-target="#modalNovoPaciente">
                        Novo Paciente
                    </button>
                    <!-- Modal -->
                    @include('modaleditar')
                </div>
            </div>
            <!-- Tabela de Atendimentos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="w-1/5 px-20 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prioridade
                                </th>
                                <th scope="col" class="w-1/5 px-20 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th scope="col" class="w-1/5 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Identificação SUS
                                </th>
                                <th scope="col" class="w-1/5 px-20 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data
                                </th>
                                <th scope="col" class="w-1/5 px-20 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ver mais
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($atendimentos as $atendimento)
                                @include('components.table-row')
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchInput3').addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const atendimentos = document.querySelectorAll('.row');

            atendimentos.forEach(function(atendimento) {
                const atendimentoText = atendimento.textContent.toLowerCase();
                if (atendimentoText.includes(searchText)) {
                    atendimento.style.display = '';
                } else {
                    atendimento.style.display = 'none';
                }
            });
        });

        function meusEncaminhamentos(tipoUsuario) {
            const form = document.getElementById('dashboardForm');
            const checkbox = document.getElementById('switchVerAtendimentos');

            if (checkbox.checked) {            
                form.action = '{{ route('dashboard', ['search' => '']) }}' + tipoUsuario;
            } else {           
                form.action = "{{ route('dashboard') }}";
            }            
            form.submit();
        }

        function resgatarDados(atendimento) {
            atendimento = JSON.parse(atendimento);
            document.getElementById('nome').value = atendimento.nome;
            document.getElementById('idade').value = atendimento.idade;
            if(atendimento.sexo === 'masculino') {
                document.getElementById('masculino').checked = true;
            } else {
                document.getElementById('feminino').checked = true;
            }
            document.getElementById('contato').value = atendimento.contato;
            document.getElementById('data-nascimento').value = atendimento.data_nascimento;
            document.getElementById('cartao-sus').value = atendimento.cartao_sus;
            document.getElementById('endereco').value = atendimento.endereco;
            document.getElementById('data-cadastro').value = atendimento.data_cadastro;
            document.getElementById('ubs').value = atendimento.ubs;
            document.getElementById('acs').value = atendimento.acs;
            document.getElementById('diagnostico').value = atendimento.diagnostico;
            document.getElementById('comorbidades').value = atendimento.comorbidades;
            document.getElementById('ultima-internacao').value = atendimento.ultima_internacao;
            document.getElementById('medico-responsavel').value = atendimento.medico_responsavel;
            if(atendimento.prioridade === 'alta') {
                document.getElementById('alta').checked = true;
            } else if(atendimento.prioridade === 'media'){
                document.getElementById('media').checked = true;
            } else {
                document.getElementById('baixa').checked = true;
            }
            document.getElementById('neurologicas').checked = !!atendimento.neurologicas;
            document.getElementById('dor').checked = !!atendimento.dor_descricao;
            document.getElementById('dor_descricao').value = atendimento.dor_descricao;
            document.getElementById('incapacidade').checked = !!atendimento.incapacidade;
            document.getElementById('avds').value = atendimento.incapacidade_descricao;
            document.getElementById('osteomusculares').checked = !!atendimento.osteomusculares;
            document.getElementById('motivos-osteomusculares').value = atendimento.osteomusculares_descricao;
            document.getElementById('uroginecologicas').checked = !!atendimento.uroginecologicas;
            document.getElementById('motivos-uroginecologicas').value = atendimento.uroginecologicas_descricao;
            document.getElementById('cardiovasculares').checked = !!atendimento.cardiovasculares;
            document.getElementById('motivos-cardiovasculares').value = atendimento.cardiovasculares_descricao;
            document.getElementById('respiratorias').checked = !!atendimento.respiratorias;
            document.getElementById('motivos-respiratorias').value = atendimento.respiratorias_descricao;
            document.getElementById('oncologicas').checked = !!atendimento.oncologicas;
            document.getElementById('motivos-oncologicas').value = atendimento.oncologicas_descricao;
            document.getElementById('pediatria').checked = !!atendimento.pediatria;
            document.getElementById('motivos-pediatria').value = atendimento.pediatria_descricao;
            document.getElementById('multiplas').checked = !!atendimento.pediatria;
            document.getElementById('motivos-multiplas').value = atendimento.pediatria_descricao;
        }
    </script>

    <!-- Modal Novo Paciente -->
    <div class="modal fade" id="modalNovoPaciente" tabindex="-1" role="dialog" aria-labelledby="modalNovoPacienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoPacienteLabel">Novo Paciente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('newpacientemodal')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
