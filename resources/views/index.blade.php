@extends('layout')

@section('body')
    <div class="table-container">
        <div style="width: 150px;margin-top:20px;">
            <button type="submit" onclick="clearModal();openModal();" class="btn btn-primary">Novo</button>
        </div>

        <table class="table-main" id="table-partners">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Razao Social</th>
                    <th>Nome Fantasia</th>
                    <th>CNPJ</th>
                    <th>Data Fundacao</th>
                    <th>Email Responsavel</th>
                    <th>Nome Responsavel</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="table-body">
            </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div id="edit" class="modal-edit">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">X</button>
            <div>
                <h2>Cadastrar/Editar </h2>
            </div>
            <div>
                <form action="" class="form-horizontal" id="formPartner">
                    <input type="hidden" id="id" name="id">
                    <div class="input-group">
                        <div>
                            <label for="razao_social" class="control-label">Razao Social: </label>
                            <input type="text" class="form-control" id="razao_social" name="razao_social"
                                placeholder="Razao Social">
                        </div>
                        <div>
                            <label for="nome_fantasia" class="control-label">Nome Fantasia: </label>
                            <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia"
                                placeholder="Nome Fantasia">
                        </div>
                        <div>
                            <label for="dt_fundacao" class="control-label">Data Fundação: </label>
                            <input type="date" class="form-control" id="dt_fundacao" name="dt_fundacao"
                                placeholder="Data Fundação">
                        </div>
                    </div>
                    <div class="input-group">
                        <div>
                            <label for="cnpj" class="control-label">CNPJ: </label>
                            <input type="text" class="form-control" id="cnpj" name="cnpj" placeholder="CNPJ">
                        </div>
                    </div>
                    <div class="input-group">
                        <div>
                            <label for="email_responsavel" class="control-label">Email Responsável: </label>
                            <input type="text" class="form-control" id="email_responsavel" name="email_responsavel"
                                placeholder="Email Responsável">
                        </div>
                    </div>
                    <div class="input-group">
                        <div>
                            <label for="nome_responsavel" class="control-label">Nome Responsável: </label>
                            <input type="text" class="form-control" id="nome_responsavel" name="nome_responsavel"
                                placeholder="Nome Responsável">
                        </div>
                    </div>
                    <div class="input-group">
                        <div>
                            <button type="submit" onclick="submitForm(this);" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Page Load
        $(function() {
            loadTable();
        });

        function loadTable() {
            $.getJSON('/api/v1/partners', function(partners) {
                for (let i = 0; i < partners.length; i++) {
                    ln = lineTable(partners[i]);
                    $('#table-body').append(ln);
                }
            });
        }

        function lineTable(partner) {
            ln = '<tr>';
            ln += '<td>' + partner.id + '</td>';
            ln += '<td>' + partner.razao_social + '</td>';
            ln += '<td>' + partner.nome_fantasia + '</td>';
            ln += '<td>' + partner.cnpj + '</td>';
            ln += '<td>' + partner.dt_fundacao + '</td>';
            ln += '<td>' + partner.email_responsavel + '</td>';
            ln += '<td>' + partner.nome_responsavel + '</td>';
            ln += '<td>';
            ln += '<button class="btn btn-primary" onclick="openModal();loadPartnerModal(' + partner.id +
                ');">Editar</button>';
            ln += '<button class="btn btn-danger" onclick="deletePartner(' + partner.id + ');">Apagar</button>';
            ln += '</td>';
            ln += '</tr>';
            return ln
        }

        function openModal() {
            document.getElementById('edit').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('edit').style.display = 'none';
        }

        function clearModal() {
            $('#formPartner input').val("");
        }

        function submitForm(btn) {
            if ($('#id').val() != '') {
                editarPartner(btn);
            } else {
                novoPartner(btn);
            }
        }

        function novoPartner(btn) {
            let txtBtn =
                '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span role="status"> Salvando...</span>';
            btn.innerHTML = txtBtn;
            $(btn).attr('disabled', true);

            let dadosForm = getDataForm('formPartner');

            $.post('/api/v1/partners', dadosForm, function(data) {
                    console.log('Done');
                })
                .done(function(data) {
                    partnerSucess(data, btn);
                })
                .fail(function(jqXHR, textStatus, errorThrown, data) {
                    partnerError(data, btn);
                });
        }

        function editarPartner(btn) {
            let txtBtn =
                '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span role="status"> Salvando...</span>';
            btn.innerHTML = txtBtn;
            $(btn).attr('disabled', true);

            let dadosForm = getDataForm('formPartner');

            $.ajax({
                type: "PUT",
                url: "/api/v1/partners/" + dadosForm.id,
                context: this,
                data: dadosForm,
                success: function(data) {
                    console.log('Sucess Edit');
                    partnerSucess(data, btn);
                },
                error: function(error, data) {
                    console.log(error);
                    partnerError(data, btn);
                }
            });
        }

        function getDataForm(stringIdForm) {
            stringIdForm = '#' + stringIdForm;
            let arrCampos = $(stringIdForm).serializeArray();
            ObjRetorno = {};
            for (let i = 0; i < arrCampos.length; i++) {
                let name = arrCampos[i].name;
                ObjRetorno[name] = arrCampos[i].value; //Cria um nome dinamico para o obejeto serialize
            }

            return ObjRetorno;
        }

        function partnerSucess(data, btn) {
            $(btn).attr('disabled', false);
            btn.innerHTML = 'Salvar';
            $('#formPartner input').val(""); // Limpar campos input
            closeModal();

            linhas = $('#table-partners>tbody>tr'); // Cria array com as linhas da tabela
            elemento = linhas.filter(function(i, elemento) {
                return elemento.cells[0].textContent == data.id
            }); // Busca a linha pela coluna 0

            // Se houver o elemento na tabela ele atualiza o conteudo se nao adiciona uma nova linha
            if (elemento.length > 0) {
                elemento[0].cells[0].textContent = data.id;
                elemento[0].cells[1].textContent = data.razao_social;
                elemento[0].cells[2].textContent = data.nome_fantasia;
                elemento[0].cells[3].textContent = data.cnpj;
                elemento[0].cells[4].textContent = data.dt_fundacao;
                elemento[0].cells[5].textContent = data.email_responsavel;
                elemento[0].cells[6].textContent = data.nome_responsavel;
            } else {
                ln = lineTable(data);
                $('#table-partners>tbody').append(ln); // Insere o partner na tabela
            }
        }

        function partnerError(data, btn) {
            $(btn).attr('disabled', false);
            btn.innerHTML = 'Salvar';
            console.log('Ocorreu um erro no cadastro!');
        }

        function loadPartnerModal(id) {
            $.getJSON('/api/v1/partners/' + id, function(partner) {
                let arrCampos = $('#formPartner').serializeArray();

                for (let i = 0; i < arrCampos.length; i++) {
                    let name = arrCampos[i].name;
                    $("[name=" + name + "]").val(partner[name]);
                    openModal();
                }
            });
        }

        function deletePartner(id) {
            $.ajax({
                type: "DELETE",
                url: "/api/v1/partners/" + id,
                context: this,
                success: function() {
                    console.log('Partner Apagado');
                    linhas = $('#table-partners>tbody>tr'); // Cria array com as linhas da tabela
                    elemento = linhas.filter(function(i, elemento) {
                        return elemento.cells[0].textContent == id
                    }); // Busca a linha pela coluna 0
                    if (elemento) {
                        elemento.remove(); // Remove a coluna
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    </script>
@endsection
