<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SimonaCascadingsubkegiatan;

/**
 * SimonaCascadingsubkegiatanSearch represents the model behind the search form of `frontend\models\SimonaCascadingsubkegiatan`.
 */
class SimonaCascadingsubkegiatanSearch extends SimonaCascadingsubkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsimonacascadingsubkegiatan_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refskpd_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id', 'refperiode_id', 'refpegawaibappeda_id'], 'integer'],
            [['uraian_sasaransubkegiatan', 'uraian_indikatorsubkegiatan', 'subkegiatan_target', 'subkegiatan_satuan', 'date_start', 'expired_date', 'status_simonacascadingsubkegiatan', 'nama_tahapansubkegiatan'], 'safe'],
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
        $query = SimonaCascadingsubkegiatan::find();

        // Eager load relations for performance
        $query->with(['refSubkegiatan', 'refKegiatan', 'refProgram', 'refPeriode']);

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
            'refsimonacascadingsubkegiatan_id' => $this->refsimonacascadingsubkegiatan_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refcascadingsubkegiatan_id' => $this->refcascadingsubkegiatan_id,
            'refskpd_id' => $this->refskpd_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refsubkegiatan_id' => $this->refsubkegiatan_id,
            'refperiode_id' => $this->refperiode_id,
            'refpegawaibappeda_id' => $this->refpegawaibappeda_id,
            'nama_tahapansubkegiatan' => $this->nama_tahapansubkegiatan,
            'date_start' => $this->date_start,
            'expired_date' => $this->expired_date,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasaransubkegiatan', $this->uraian_sasaransubkegiatan])
            ->andFilterWhere(['like', 'uraian_indikatorsubkegiatan', $this->uraian_indikatorsubkegiatan])
            ->andFilterWhere(['like', 'subkegiatan_target', $this->subkegiatan_target])
            ->andFilterWhere(['like', 'subkegiatan_satuan', $this->subkegiatan_satuan])
            ->andFilterWhere(['like', 'status_simonacascadingsubkegiatan', $this->status_simonacascadingsubkegiatan]);

        return $dataProvider;
    }
}
