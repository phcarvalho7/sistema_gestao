<?php
// TEMPLATE - rodapé de todas as páginas da loja.
// Fecha o <main>, mostra o rodapé e carrega o JavaScript do Bootstrap.
?>
    </main>

    <footer class="rodape mt-5">
        <div class="container py-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <h6 class="mb-1">Prosa <span class="marca-destaque">&amp; Traço</span></h6>
                    <p class="texto-mudo texto-mini mb-0">
                        Livros, HQs e mangás. Sistema de gestão de vendas feito com
                        PHP, MariaDB e TypeScript.
                    </p>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <a href="produtos" class="btn btn-contorno btn-sm">Catálogo</a>
                    <a href="admin/" target="_blank" class="btn btn-primario btn-sm">Painel</a>
                </div>
            </div>
            <hr>
            <p class="texto-mudo texto-mini text-center mb-0">
                &copy; <?= date("Y") ?> Prosa &amp; Traço - Sistema de Gestão de Vendas
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
