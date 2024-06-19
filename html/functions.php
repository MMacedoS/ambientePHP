<!DOCTYPE html>
<html>
<head>
  <title>Operações CRUD com Spinner de Carregamento</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #3498db;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      animation: spin 2s linear infinite;
      margin: 20px auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
  <script>
  
  </script>
</head>
<body>
  <!-- Exemplo de uso das funções -->
  <script>
    // Criar novo registro
    var createUrl = 'https://api.example.com/data';
    var createData = { name: 'Novo Registro', value: 10 };
    createData(createUrl, createData);

    // Atualizar registro existente
    var updateUrl = 'https://api.example.com/data/1';
    var updateData = { name: 'Registro Atualizado', value: 20 };
    updateData(updateUrl, updateData);

    // Exibir registro existente
    var showUrl = 'https://api.example.com/data/1';
    showData(showUrl);

    // Excluir registro existente
    var deleteUrl = 'https://api.example.com/data/1';
    deleteData(deleteUrl);
  </script>
</body>
</html>
