import './App.css';
import React from 'react';
import axios from 'axios';
import swal from "sweetalert";
import {loadProgressBar} from "axios-progress-bar";
import 'axios-progress-bar/dist/nprogress.css'
import fileDownload from 'js-file-download'

export default class App extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            files: [],
        }
    }

    handleChange = e => {
        this.setState({[e.target.name]: e.target.value})
    }

    handleFileChange = (e) => {
        this.setState({files: e.target.files})
    }

    cancelSelection = () => {
        this.setState({files: ''})
    }

    handleSubmitFiles = (e) => {
        if (this.state.name.length === 0) {
            swal({icon: 'error', text: "Veuillez spécifié un nom de fichier !"})
        } else {
            loadProgressBar();
            e.preventDefault();
            const fd = new FormData();
            let i = 0;
            for (const key of Object.keys(this.state.files)) {
                fd.append(`file-${i}`, this.state.files[key])
                ++i
            }
            axios.post(`compress.php?name=${this.state.name}`, fd)
                .then(({data}) => {
                    swal({
                        icon: 'success',
                        title: 'Files',
                        text: 'Files was uploaded',
                    })
                    //this.setState({files: ''})
                })
                .catch((err) => {
                    console.log(err)
                    swal({
                        icon: 'error',
                        title: 'Files',
                        text: 'Error server can\'t upload your files',
                    })
                })
        }
    }

    handleDownload = (e) => {
        loadProgressBar();
        axios.get(`download.php?name=${this.state.name}`, {
            responseType: 'blob',
        })
            .then((res) => {
                fileDownload(res.data, this.state.name + '.mytar')
            })
            .catch((err) => {
                swal({
                    icon: 'error',
                    text: 'le fichier souhaité est corrompu ou introuvable.'
                })
            })
    }

    render() {
        return (
            <body>
            <div>
                <section className=" text-gray-200">
                    <div className="max-w-6xl mx-auto px-5 py-24 ">

                        <div className="flex flex-wrap w-full mb-20 flex-col items-center text-center">
                            <h1 className=" title-font mb-2 text-4xl font-extrabold leading-10 tracking-tight text-left sm:text-5xl sm:leading-none md:text-6xl"> Rush
                                2</h1>
                            <p className="lg:w-1/2 w-full leading-relaxed text-base">W1 - Piscine PHP - W-WEB-024 -
                                my_tar</p>
                        </div>

                        <div className="flex flex-wrap -m-4">
                            <div className="xl:w-1/3 md:w-1/2 p-4">
                                <div className="border border-gray-300 p-6 rounded-lg">
                                    <div
                                        className="w-10 h-10 inline-flex items-center justify-center rounded-full bg-indigo-100 text-indigo-500 mb-4 italic">
                                        <span className="font-bold text-sm "> Php</span>
                                    </div>

                                    <h2 className="text-lg  font-medium title-font mb-2">La compression par PHP </h2>
                                    <p className="leading-relaxed text-base">Écriture de deux programmes exécutables
                                        "my_tar.php" et "my_untar.php".</p><br></br><br></br>

                                    <div className="text-center mt-2 leading-none flex justify-between w-full">
                                        <span className=" mr-3 inline-flex items-center leading-none text-sm  py-1 ">Étape 1 - 2</span>
                                    </div>
                                </div>
                            </div>

                            <div className="xl:w-1/3 md:w-1/2 p-4">
                                <div className="border border-gray-300 p-6 rounded-lg">
                                    <div
                                        className="w-10 h-10 inline-flex items-center justify-center rounded-full bg-indigo-100 text-indigo-500 mb-4 italic">
                                        <span className="font-bold text-sm ">React</span>
                                    </div>

                                    <h2 className="text-lg  font-medium title-font mb-2">Création de l'interface
                                        Web</h2>
                                    <p className="leading-relaxed text-base">Création grâce à React de l'interface
                                        graphique qui comprend la conception du graphisme général pour toutes les pages
                                        du site.</p><br></br>
                                    <div className="text-center mt-2 leading-none flex justify-between w-full">
                                        <span className=" mr-3 inline-flex items-center leading-none text-sm  py-1 ">Étape 3</span>
                                    </div>
                                </div>
                            </div>

                            <div className="xl:w-1/3 md:w-1/2 p-4">
                                <div className="border border-gray-300 p-6 rounded-lg">
                                    <div
                                        className="w-10 h-10 inline-flex items-center justify-center rounded-full bg-indigo-100 text-indigo-500 mb-4 italic">
                                        <span className="font-bold text-sm "> CSS</span>
                                    </div>
                                    <h2 className="text-lg  font-medium title-font mb-2">Bonus - CSS avancé</h2>
                                    <p className="leading-relaxed text-base">Ajout de feuilles de style en cascade pour
                                        mettre en forme la page Web.</p><br></br><br></br>
                                    <div className="text-center mt-2 leading-none flex justify-between w-full">
                                        <span className=" mr-3 inline-flex items-center leading-none text-sm  py-1 ">Étape bonus</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <main className="container mx-auto max-w-screen-lg h-full mb-10">
                    <label htmlFor="name" className={"text-bold"}>Donner un nom à votre archive : </label>
                    <input type="text" name='name' placeholder={"Put a name for your archive"} id='name'
                           onChange={this.handleChange}/><br></br>
                    <article aria-label="File Upload Modal"
                             className="relative h-full flex flex-col bg-white shadow-xl rounded-md">
                        <section className="h-full overflow-auto p-8 w-full h-full flex flex-col">
                            <header className="border border-dashed border-gray-500 relative"
                                    className="border-dashed border-2 border-gray-400 py-12 flex flex-col justify-center items-center">
                                <p className="mb-3 font-semibold text-gray-900 flex flex-wrap justify-center"><span>Upload your files to archive</span>
                                </p>
                                <div className={"flex justify-content-center"}>
                                    <input id="hidden-input" type="file" multiple name="images"
                                           onChange={this.handleFileChange}
                                           className="mt-2 rounded-sm px-3 py-1 border border-none focus:shadow-outline focus:outline-none"/>
                                </div>
                            </header>
                        </section>
                        <section className="flex justify-center px-8 pb-8 pt-4">
                            <button id="submit" onClick={this.handleSubmitFiles} className="btna"> Generate my archive
                            </button>
                        </section>
                        <section align="center"><i>*PDF files and images files cannot be archived.</i></section>
                        <button className="btnd" onClick={this.handleDownload} disabled={!this.state.name}> Download my
                            archive
                        </button>
                    </article>
                    <section align="center" className="my-3"><i className="text-white">© Pierre Schaefer, Adrien Marion,
                        Judikael Bellance - W@C 2020.</i></section>

                </main>
            </div>
            </body>
        );
    }
}