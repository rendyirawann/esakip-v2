<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipIndikatorcascadingsubkegiatan;

/**
 * SakipIndikatorcascadingsubkegiatanSearch represents the model behind the search form of `frontend\models\SakipIndikatorcascadingsubkegiatan`.
 */
class SakipIndikatorcascadingsubkegiatanSearch extends SakipIndikatorcascadingsubkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorsubkegiatan_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refskpd_id', 'refperiode_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id'], 'integer'],
            [['target_rkt', 'anggaran_rkt', 'target_rkt_p', 'anggaran_rkt_p', 'target_pk', 'anggaran_pk', 'target_pk_p', 'anggaran_pk_p', 'realisasi', 'capaian', 'keterangan', 'keterangan_pk', 'keterangan_pk_p', 'analisis'], 'safe'],
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
        $query = SakipIndikatorcascadingsubkegiatan::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'refCascadingSubkegiatan']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->query->orderBy(['refsasaranrenstra_id' => SORT_ASC, 'refsubkegiatan_id' => SORT_ASC]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refindikatorsubkegiatan_id' => $this->refindikatorsubkegiatan_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refcascadingsubkegiatan_id' => $this->refcascadingsubkegiatan_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refsubkegiatan_id' => $this->refsubkegiatan_id,
        ]);

        $query->andFilterWhere(['like', 'target_rkt', $this->target_rkt])
            ->andFilterWhere(['like', 'anggaran_rkt', $this->anggaran_rkt])
            ->andFilterWhere(['like', 'target_rkt_p', $this->target_rkt_p])
            ->andFilterWhere(['like', 'anggaran_rkt_p', $this->anggaran_rkt_p])
            ->andFilterWhere(['like', 'target_pk', $this->target_pk])
            ->andFilterWhere(['like', 'anggaran_pk', $this->anggaran_pk])
            ->andFilterWhere(['like', 'target_pk_p', $this->target_pk_p])
            ->andFilterWhere(['like', 'anggaran_pk_p', $this->anggaran_pk_p])
            ->andFilterWhere(['like', 'realisasi', $this->realisasi])
            ->andFilterWhere(['like', 'capaian', $this->capaian])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'keterangan_pk', $this->keterangan_pk])
            ->andFilterWhere(['like', 'keterangan_pk_p', $this->keterangan_pk_p])
            ->andFilterWhere(['like', 'analisis', $this->analisis]);

        return $dataProvider;
    }
}
