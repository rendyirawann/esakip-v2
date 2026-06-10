<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan;

/**
 * SakipIndikatorcascadingsubkegiatanTriwulanSearch represents the model behind the search form of `frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan`.
 */
class SakipIndikatorcascadingsubkegiatanTriwulanSearch extends SakipIndikatorcascadingsubkegiatanTriwulan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorsubkegiatantriwulan_id', 'reftriwulan_id', 'refindikatorsubkegiatan_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refskpd_id', 'refperiode_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id'], 'integer'],
            [['triwulan_target_rkt', 'triwulan_target_rkt_p', 'triwulan_target_pk', 'triwulan_target_pk_p', 'triwulan_realisasi', 'triwulan_capaian', 'triwulan_keterangan', 'triwulan_keterangan_pk_p', 'triwulan_analisis', 'triwulan_penyerapan_anggaran'], 'safe'],
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
        $query = SakipIndikatorcascadingsubkegiatanTriwulan::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'refIndikatorCascadingSubkegiatan']);

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
            'refindikatorsubkegiatantriwulan_id' => $this->refindikatorsubkegiatantriwulan_id,
            'refindikatorsubkegiatan_id' => $this->refindikatorsubkegiatan_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refcascadingsubkegiatan_id' => $this->refcascadingsubkegiatan_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
            'reftriwulan_id' => $this->reftriwulan_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refsubkegiatan_id' => $this->refsubkegiatan_id,
        ]);

        $query->andFilterWhere(['like', 'triwulan_target_rkt', $this->triwulan_target_rkt])
            ->andFilterWhere(['like', 'triwulan_target_rkt_p', $this->triwulan_target_rkt_p])
            ->andFilterWhere(['like', 'triwulan_target_pk', $this->triwulan_target_pk])
            ->andFilterWhere(['like', 'triwulan_target_pk_p', $this->triwulan_target_pk_p])
            ->andFilterWhere(['like', 'triwulan_realisasi', $this->triwulan_realisasi])
            ->andFilterWhere(['like', 'triwulan_capaian', $this->triwulan_capaian])
            ->andFilterWhere(['like', 'triwulan_keterangan', $this->triwulan_keterangan])
            ->andFilterWhere(['like', 'triwulan_keterangan_pk_p', $this->triwulan_keterangan_pk_p])
            ->andFilterWhere(['like', 'triwulan_analisis', $this->triwulan_analisis])
            ->andFilterWhere(['like', 'triwulan_penyerapan_anggaran', $this->triwulan_penyerapan_anggaran]);

        return $dataProvider;
    }
}
