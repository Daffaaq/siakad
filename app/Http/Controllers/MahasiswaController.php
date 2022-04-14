<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\kelas;
use App\Models\MataKuliah;
use App\Models\Mahasiswa_MataKuliah;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request('search')){
            $paginate = Mahasiswa::where('nim', 'like', '%'.request('search').'%')
                                    ->orwhere('nama', 'like', '%'.request('search').'%')
                                    ->orwhere('kelas', 'like', '%'.request('search').'%')
                                    ->orwhere('jurusan', 'like', '%'.request('search').'%')
                                    // ->orwhere('jenis_kelamin', 'like', '%'.request('search').'%')
                                    // ->orwhere('email', 'like', '%'.request('search').'%')
                                    // ->orwhere('alamat', 'like', '%'.request('search').'%')
                                    // ->orwhere('tanggal_lahir', 'like', '%'.request('search').'%')
                                    ->paginate(3);
            return view('mahasiswa.index', ['paginate'=>$paginate]);
        } else {
            //yang semula Mahasiswa::all, diubah menjadi with() yang menyatakan relasi
            $mahasiswa = Mahasiswa::with('kelas')->get(); 
            $paginate=Mahasiswa::orderBy('id_mahasiswa','asc')->paginate(3);
            return view('mahasiswa.index',['mahasiswa'=>$mahasiswa,'paginate'=>$paginate]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswa.create',['kelas' => $kelas]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // //melakukan validasi data
        $request->validate([
        'Nim' => 'required',
        'Nama' => 'required',
        'Kelas' => 'required',
        'Jurusan' => 'required',
        // 'Jenis_Kelamin' => 'required',
        // 'Email' => 'required',
        // 'Alamat' => 'required',
        // 'Tanggal_Lahir' => 'required',
        ]);

        $mahasiswa = new Mahasiswa;
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->kelas_id = $request->get('Kelas');
        $mahasiswa->jurusan = $request->get('Jurusan');
        $mahasiswa->save();
        // $mahasiswa->Jenis_Kelamin = $request->get('Jenis_Kelamin');
        // $mahasiswa->Email = $request->get('Email');
        // $mahasiswa->Alamat = $request->get('Alamat');
        // $mahasiswa->Tanggal_Lahir = $request->get('Tanggal_Lahir');

        // $kelas = new Kelas;
        // $kelas->id = $request->get('kelas');

        // // fungsi eloquent untuk menambah data dengan relasi belongsTo
        // $mahasiswa->kelas()->associate($kelas);
        // $mahasiswa->save();

        //jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('mahasiswa.index')
        ->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($nim)
    {
        //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa
        //code sebelum dibuat relasi --> $mahasiswa = Mahasiswa::find($Nim);
        $Mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();

        return view('mahasiswa.detail', compact('Mahasiswa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($nim)
    {
        //menampilkan detail data dengan menemukan berdasarkan Nim Mahasiswa untuk diedit
        $Mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        $kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswa.edit', compact('Mahasiswa','kelas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $nim)
    {
        $request->validate([
        'Nim' => 'required',
        'Nama' => 'required',
        'Kelas' => 'required',
        'Jurusan' => 'required',
        // 'Jenis_Kelamin' => 'required',
        // 'Email' => 'required',
        // 'Alamat' => 'required',
        // 'Tanggal_Lahir' => 'required',
        ]);

        //fungsi eloquent untuk mengupdate data inputan kita
        // Mahasiswa::where('nim', $nim) ->update([
        // 'nim'=>$request->Nim,
        // 'nama'=>$request->Nama,
        // 'kelas'=>$request->Kelas,
        // 'jurusan'=>$request->Jurusan,
        // 'jenis_kelamin'=>$request->Jenis_Kelamin,
        // 'email'=>$request->Email,
        // 'alamat'=>$request->Alamat,
        // 'tanggal_lahir'=>$request->Tanggal_Lahir,
        // ]);

        $mahasiswa = Mahasiswa::with('kelas')->where('nim',$nim)->first();
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->kelas_id = $request->get('Kelas');
        $mahasiswa->jurusan = $request->get('Jurusan');
        $mahasiswa->save();

        //jika data berhasil diupdate, akan kembali ke halaman utama
        return redirect()->route('mahasiswa.index')
        ->with('success', 'Mahasiswa Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($nim)
    {
        //fungsi eloquent untuk menghapus data
        Mahasiswa::where('nim', $nim)->delete();
        return redirect()->route('mahasiswa.index')
        -> with('success', 'Mahasiswa Berhasil Dihapus');
    }

    public function nilai($nim)
    {
        // $list = Mahasiswa_Matakuliah::with("matakuliah")->where("mahasiswa_id", $nim)->get();
        $mhs = Mahasiswa::with('kelas')->where("nim", $nim)->first();
        $matkul = Mahasiswa_Matakuliah::with("matakuliah")->where("mahasiswa_id", ($mhs -> id_mahasiswa))->get();
        // $matkul = Mahasiswa_Matakuliah::where('matakuliah_id', ($mhs -> id_mahasiswa))->first();
        return view('mahasiswa.nilai', ['mahasiswa' => $mhs,'matakuliah'=>$matkul]);
        //return dd($matkul);
    }
}
