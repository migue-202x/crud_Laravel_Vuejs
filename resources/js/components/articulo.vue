

<!--
Vue tiene tres partes:

1) La parte html que esta dentro de un template
2) La parte de la logica que esta dentro de un script
3) La parte de los estilos dentro de etiquetes <style>
-->

<!------------------------------------------------------------------>
<template>
    <div>
        <h1 class="text-center">Gestión de Artículos</h1>
        <hr>


        <!-- Button to Open the Modal -->
        <!-- yo no voy abrir el modal como lo utiliza jquery, voy a manejar el modal utilizando vue -->
        <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"> -->
        <button @click="modificar = false; abrirModal();" type="button" class="btn btn-primary my-4">Nuevo</button>

        <!-- The Modal -->
        <!-- No necesito el id -->
        <!-- <div class="modal" id="myModal"> -->
        <div class="modal" :class="{mostrar:modal}">
            <div class="modal-dialog">
                <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ tituloModal }}</h4>
                    <button @click="cerrarModal();" type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="my-4">
                        <label for="">Nombre</label>
                        <input v-model="articulo.nombre" type="text" class="form-control" id="nombre" placeholder="nombre del articulo">
                        <span class="text-danger" v-if="errores.nombre">{{ errores.nombre[0] }}</span>
                    </div>
                    <div class="my-4">
                        <label for="">Descripcion</label>
                        <input v-model="articulo.descripcion" type="text" class="form-control" id="descripcion" placeholder="descripcion del articulo">
                        <span class="text-danger" v-if="errores.descripcion">{{ errores.descripcion[0] }}</span>
                    </div>
                    <div class="my-4">
                        <label for="">Stock</label>
                        <input v-model="articulo.stock" type="number" class="form-control" id="stock" placeholder="stock del articulo">
                        <span class="text-danger" v-if="errores.stock">{{ errores.stock[0] }}</span>
                    </div>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button @click="cerrarModal();" type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button @click="guardar();" type="button" class="btn btn-success" data-dismiss="modal">Guardar</button>
                </div>

                </div>
            </div>
        </div>

        <!-- Color a cada fila de la tabla -->
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripcion</th>
                    <th scope="col">Stock</th>


                    <!-- colspan="2" -> para que se conbinen dos columnas -->
                    <!-- lass="text-center" -> texto centrado -->
                    <th scope="col" colspan="2" class="text-center">Accion</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="art in articulos.data" :key="art.id">
                    <th scope="row">{{ art.id }}</th>
                    <td>{{ art.nombre }}</td>
                    <td>{{ art.descripcion }}</td>
                    <td>{{ art.stock }}</td>

                    <td>
                        <button @click="modificar = true; abrirModal(art);" class="btn btn-warning">Editar</button>
                    </td>
                    <td>
                        <button @click="eliminar(art.id)" class="btn btn-danger">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-4 md-4 text-right text-primary">
                {{ articulos.from }} - {{ articulos.to }} /total:{{ articulos.total }}
            </div>
            <div class="col-2 md-2">
                <select class="custom-select" v-model="pagination.per_page" @change="listar();">
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="8">8</option>
                </select>
            </div>


            <div class="col-6 md-6">
                <nav>
                    <ul class="pagination">
                        <li class="page-item" :class="{ disabled:pagination.page == 1 }"><a href="#" class="page-link" @click="pagination.page=1; listar();">&laquo;</a></li>
                        <li class="page-item" :class="{ disabled:pagination.page == 1 }"><a href="#" class="page-link" @click="pagination.page--; listar();">&lt;</a></li>
                        <li class="page-item" v-for="n in paginas" :key="n" :class="{ active:pagination.page == n }"><a href="#" class="page-link" @click="pagination.page=n; listar();">{{ n }}</a></li>
                        <li class="page-item" :class="{ disabled:pagination.page == articulos.last_page }"><a href="#" class="page-link" @click="pagination.page++; listar();">&gt;</a></li>
                        <li class="page-item" :class="{ disabled:pagination.page == articulos.last_page }"><a href="#" class="page-link" @click="pagination.page=articulos.last_page;">&raquo;</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>
<!------------------------------------------------------------------>
<script>
    export default {
        //necesito una data para recorrer con un v-for
        data(){
            return{
                //objeto para crear y editar articulos
                articulo:{
                    nombre:'yy',
                    descripcion:'yuu',
                    stock:1,
                },

                //uso variable id para poder editar
                id:0,

                modificar:true,
                //0 modal cerrado | 1 modal abierto
                modal:0,

                //voy a midifcar el titulo del modal cuando creo y edito
                tituloModal: '',

                //para guardar los articulos en un array necesito un metodo
                articulos:[],

                //array para almacenar los errores
                errores:{},

                //paginacion. Por defecto la pagina 1
                pagination:{
                    page:1,
                    per_page:5,
                },

                paginas:[],
            }
        },
        //aca voy a crear todos los metodos. Crear | Listar  | Borrar | etc.
        methods: {
            //voy a accesar a la ruta index solo a traves de Axios. No estoy usando 'API REST'
            //no voy a utilizar promesas, por uso uso async (codigo moderno de js)
            async listar(){
                const res = await axios.get('/articulos', {params: this.pagination});
                this.articulos = res.data;
                this.listarPaginas();
            },

            listarPaginas(){
                const n = 2
                let arrayN = []
                let ini = this.pagination.page - 2
                if(ini<1){
                    ini = 1
                }
                 let fin = this.pagination.page + 2
                if(fin>this.articulos.last_page){
                    fin = this.articulos.last_page
                }

                for(let i=ini; i<=fin; i++){
                    arrayN.push(i)
                }

                this.paginas = arrayN
            },

            async eliminar(id){
                const res = await axios.delete('/articulos/'+id);
                this.listar();
            },

            async guardar(id){
                try {
                    if(this.modificar){
                    const res = await axios.put('/articulos/'+ this.id, this.articulo);
                    }
                    else{
                        const res = await axios.post('/articulos', this.articulo);
                    }
                    this.cerrarModal();
                    this.listar();
                } catch (error) {
                    if(error.response.data){
                        this.errores = error.response.data.errors
                    }
                }

            },

            //antes le pasaba id (id = 0). Ahora le paso todo el objeto
            abrirModal(data ={}){
                this.modal = 1;

                if (this.modificar){
                    this.id = data.id;
                    this.tituloModal = 'Modificar Articulo';
                    this.articulo.nombre = data.nombre;
                    this.articulo.descripcion = data.descripcion;
                    this.articulo.stock = data.stock;

                }else{
                    this.id = 0;
                    this.tituloModal = 'Crear Articulo'
                    this.articulo.nombre = '';
                    this.articulo.descripcion = '';
                    this.articulo.stock = 1;

                }
            },

            cerrarModal(){
                this.modal = 0;
                this.errores={};
            },

        },
        //un componente tiene un ciclo de vida. Puedo usar: moment | create | etc.
        created(){
           this.listar();
        },
    }

</script>
<!------------------------------------------------------------------>
<style>
    /* estilo para mostrar el modal */
    .mostrar{
        display: list-item;
        opacity: 1;
        background: RGBA( 255 , 0 , 0, 0.5 );
    }
</style>
