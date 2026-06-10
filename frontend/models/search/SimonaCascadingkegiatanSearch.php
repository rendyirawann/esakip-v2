<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SimonaCascadingkegiatan;

/**
 * SimonaCascadingkegiatanSearch represents the model behind the search form of `frontend\models\SimonaCascadingkegiatan`.
 */
class SimonaCascadingkegiatanSearch extends SimonaCascadingkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsimonacascadingkegiatan_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refskpd_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id', 'refperiode_id', 'refpegawaibappeda_id'], 'integer'],
            [['uraian_sasarankegiatan', 'uraian_indikatorkegiatan', 'kegiatan_target', 'kegiatan_satuan', 'date_start', 'expired_date', 'status_simonacascadingkegiatan', 'nama_tahapankegiatan'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SimonaCascadingkegiatan::find();

        // Eager load relations for performance
        $query->with(['refProgram', 'refKegiatan', 'refPeriode']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refsimonacascadingkegiatan_id' => $this->refsimonacascadingkegiatan_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refskpd_id' => $this->refskpd_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refperiode_id' => $this->refperiode_id,
            'refpegawaibappeda_id' => $this->refpegawaibappeda_id,
            'date_start' => $this->date_start,
            'expired_date' => $this->expired_date,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasarankegiatan', $this->uraian_sasarankegiatan])
            ->andFilterWhere(['like', 'uraian_indikatorkegiatan', $this->uraian_indikatorkegiatan])
            ->andFilterWhere(['like', 'nama_tahapankegiatan', $this->nama_tahapankegiatan])
            ->andFilterWhere(['like', 'kegiatan_target', $this->kegiatan_target])
            ->andFilterWhere(['like', 'kegiatan_satuan', $this->kegiatan_satuan])
            ->andFilterWhere(['like', 'status_simonacascadingkegiatan', $this->status_simonacascadingkegiatan]);

        return $dataProvider;
    }
}
